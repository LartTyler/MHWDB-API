<?php
	namespace App\Controller;

	use App\Contrib\ApiErrors\CreateError;
	use App\Contrib\ApiErrors\InvalidPayloadError;
	use App\Contrib\ApiErrors\UpdateError;
	use App\Event\Events\ApiEntityCreateEvent;
	use App\Event\Events\ApiEntityDeleteEvent;
	use App\Event\Events\ApiEntityUpdateEvent;
	use App\QueryDocument\ApiQueryManager;
	use App\QueryDocument\Projection;
	use App\Response\NoContentResponse;
	use App\Response\SearchError;
	use DaybreakStudios\RestApiCommon\Error\ApiErrorInterface;
	use DaybreakStudios\RestApiCommon\Error\Errors\DoctrineQueryDocument\EmptyQueryError;
	use DaybreakStudios\RestApiCommon\Error\Errors\DoctrineQueryDocument\ProjectionSyntaxError;
	use DaybreakStudios\RestApiCommon\Error\Errors\DoctrineQueryDocument\QuerySyntaxError;
	use DaybreakStudios\RestApiCommon\Error\Errors\NotFoundError;
	use DaybreakStudios\RestApiCommon\Error\Errors\Validation\ValidationFailedError;
	use DaybreakStudios\RestApiCommon\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\EntityTransformerInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ConstraintViolationException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
	use Symfony\Component\EventDispatcher\EventDispatcherInterface;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	abstract class AbstractController extends BaseAbstractController {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ResponderService
		 */
		protected $responder;

		/**
		 * @var EventDispatcherInterface
		 */
		protected $eventDispatcher;

		/**
		 * @var string
		 */
		protected $entityClass;

		/**
		 * AbstractCrudController constructor.
		 *
		 * @param string $entityClass
		 */
		public function __construct(string $entityClass) {
			$this->entityClass = $entityClass;
		}

		/**
		 * @required
		 *
		 * @param EntityManagerInterface $entityManager
		 *
		 * @return void
		 */
		public function setEntityManager(EntityManagerInterface $entityManager): void {
			$this->entityManager = $entityManager;
		}

		/**
		 * @required
		 *
		 * @param ResponderService $responder
		 *
		 * @return void
		 */
		public function setResponder(ResponderService $responder): void {
			$this->responder = $responder;
		}

		/**
		 * @required
		 *
		 * @param EventDispatcherInterface $eventDispatcher
		 *
		 * @return void
		 */
		public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void {
			$this->eventDispatcher = $eventDispatcher;
		}

		/**
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			if ($request->query->has('q')) {
				$query = $request->query->all();

				if ($limit = ($query['limit'] ?? null))
					unset($query['limit']);

				if ($offset = ($query['offset'] ?? null))
					unset($query['offset']);

				if (!sizeof($query))
					return $this->respond(new EmptyQueryError());

				$queryBuilder = $this->entityManager->createQueryBuilder()
					->from($this->entityClass, 'e')
					->select('e')
					->setMaxResults($limit)
					->setFirstResult($offset);

				$queryObject = $query['q'] ?? [];

				if (is_string($queryObject)) {
					$queryObject = json_decode($queryObject, true);

					if (json_last_error() !== JSON_ERROR_NONE)
						return $this->respond(new QuerySyntaxError());
				}

				if (!$queryObject)
					return $this->respond(new EmptyQueryError());

				try {
					$this->get(ApiQueryManager::class)->apply($queryBuilder, $queryObject);
				} catch (\Exception $e) {
					return $this->respond(new SearchError($e->getMessage()));
				}

				$results = $queryBuilder->getQuery()->getResult();
			} else
				$results = $this->entityManager->getRepository($this->entityClass)->findAll();

			return $this->respond($results);
		}

		/**
		 * @param EntityTransformerInterface $transformer
		 * @param Request                    $request
		 *
		 * @return Response
		 */
		protected function doCreate(EntityTransformerInterface $transformer, Request $request): Response {
			$payload = json_decode($request->getContent());

			if (json_last_error() !== JSON_ERROR_NONE)
				return $this->respond(new InvalidPayloadError());

			try {
				$entity = $transformer->create($payload);
			} catch (EntityTransformerException $exception) {
				if ($exception instanceof ConstraintViolationException)
					return $this->respond(new ValidationFailedError($exception->getErrors()));

				return $this->respond(new CreateError($exception->getMessage()));
			}

			/** @noinspection PhpParamsInspection */
			/** @noinspection PhpMethodParametersCountMismatchInspection */
			$this->eventDispatcher->dispatch(ApiEntityCreateEvent::NAME, new ApiEntityCreateEvent($entity, $payload));

			$this->entityManager->flush();

			return $this->respond($entity);
		}

		/**
		 * @param EntityTransformerInterface $transformer
		 * @param EntityInterface            $entity
		 * @param Request                    $request
		 *
		 * @return Response
		 */
		protected function doUpdate(
			EntityTransformerInterface $transformer,
			EntityInterface $entity,
			Request $request
		): Response {
			$payload = json_decode($request->getContent());

			if (json_last_error() !== JSON_ERROR_NONE)
				return $this->respond(new InvalidPayloadError());

			try {
				$transformer->update($entity, $payload);
			} catch (EntityTransformerException $exception) {
				if ($exception instanceof ConstraintViolationException)
					return $this->respond(new ValidationFailedError($exception->getErrors()));

				return $this->respond(new UpdateError($exception->getMessage()));
			}

			/** @noinspection PhpParamsInspection */
			/** @noinspection PhpMethodParametersCountMismatchInspection */
			$this->eventDispatcher->dispatch(ApiEntityUpdateEvent::NAME, new ApiEntityUpdateEvent($entity, $payload));

			$this->entityManager->flush();

			return $this->respond($entity);
		}

		/**
		 * @param EntityTransformerInterface $transformer
		 * @param EntityInterface            $entity
		 *
		 * @return Response
		 */
		protected function doDelete(EntityTransformerInterface $transformer, EntityInterface $entity): Response {
			$transformer->delete($entity);

			/** @noinspection PhpParamsInspection */
			/** @noinspection PhpMethodParametersCountMismatchInspection */
			$this->eventDispatcher->dispatch(ApiEntityDeleteEvent::NAME, new ApiEntityDeleteEvent($entity));

			$this->entityManager->flush();

			return new NoContentResponse();
		}

		/**
		 * @param ApiErrorInterface|array|object|null $data
		 * @param Request|null                        $request
		 *
		 * @return Response
		 */
		protected function respond($data, Request $request = null): Response {
			if (!$request)
				$request = $this->get('request_stack')->getCurrentRequest();

			$fields = $request->query->get('p');

			if ($fields) {
				$fields = @json_decode($fields, true);

				if (json_last_error() !== JSON_ERROR_NONE)
					return $this->responder->createErrorResponse(new ProjectionSyntaxError());
			}

			try {
				$projection = Projection::fromFields($fields ?: []);
			} catch (\InvalidArgumentException $e) {
				return $this->responder->createErrorResponse(new SearchError($e->getMessage()));
			}

			if ($data instanceof ApiErrorInterface)
				return $this->responder->createErrorResponse($data);
			else if ($data instanceof Response)
				return $data;
			else if ($data === null)
				return $this->responder->createErrorResponse(new NotFoundError());

			if (is_array($data))
				$data = $this->normalizeMany($data, $projection);
			else if ($data instanceof EntityInterface)
				$data = $projection->filter($this->normalizeOne($data, $projection));

			if ($data === null)
				$status = Response::HTTP_NO_CONTENT;
			else
				$status = Response::HTTP_OK;

			return new JsonResponse(
				$data,
				$status,
				[
					'Content-Type' => 'application/json',
				]
			);
		}

		/**
		 * @param EntityInterface[] $entities
		 * @param Projection        $projection
		 *
		 * @return array
		 */
		protected function normalizeMany(array $entities, Projection $projection): array {
			$normalized = [];

			foreach ($entities as $entity)
				$normalized[] = $projection->filter($this->normalizeOne($entity, $projection));

			return $normalized;
		}

		/**
		 * @param EntityInterface|null $entity
		 * @param Projection           $projection
		 *
		 * @return array|null
		 */
		protected abstract function normalizeOne(?EntityInterface $entity, Projection $projection): ?array;
	}