<?php
	namespace App\Controller;

	use App\Entity\SluggableInterface;
	use App\QueryDocument\ApiQueryManager;
	use App\QueryDocument\Projection;
	use App\Response\BadProjectionObjectError;
	use App\Response\BadQueryObjectError;
	use App\Response\EmptySearchParametersError;
	use App\Response\SearchError;
	use App\Response\SlugNotSupportedError;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\RouterInterface;

	abstract class AbstractDataController extends Controller {
		/**
		 * @var EntityManager
		 */
		protected $manager;

		/**
		 * @var ResponderService
		 */
		protected $responder;

		/**
		 * @var RouterInterface
		 */
		protected $router;

		/**
		 * @var string
		 */
		protected $entityClass;

		/**
		 * AbstractCrudController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 * @param string            $entityClass
		 */
		public function __construct(
			RegistryInterface $doctrine,
			ResponderService $responder,
			RouterInterface $router,
			string $entityClass
		) {
			$this->manager = $doctrine->getManager();
			$this->responder = $responder;
			$this->router = $router;
			$this->entityClass = $entityClass;
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
					return $this->respond(new EmptySearchParametersError());

				$queryBuilder = $this->manager->createQueryBuilder()
					->from($this->entityClass, 'e')
					->select('e')
					->setMaxResults($limit)
					->setFirstResult($offset);

				$queryObject = $query['q'] ?? [];

				if (is_string($queryObject)) {
					$queryObject = json_decode($queryObject, true);

					if (json_last_error() !== JSON_ERROR_NONE)
						return $this->respond(new BadQueryObjectError());
				}

				if (!$queryObject)
					return $this->respond(new EmptySearchParametersError());

				try {
					$this->get(ApiQueryManager::class)->apply($queryBuilder, $queryObject);
				} catch (\Exception $e) {
					return $this->respond(new SearchError($e->getMessage()));
				}

				$results = $queryBuilder->getQuery()->getResult();
			} else
				$results = $this->manager->getRepository($this->entityClass)->findAll();

			return $this->respond($results);
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return Response
		 */
		public function read(string $idOrSlug): Response {
			if (is_numeric($idOrSlug))
				$entity = $this->manager->getRepository($this->entityClass)->find((int)$idOrSlug);
			else {
				if (!is_a($this->entityClass, SluggableInterface::class, true))
					return $this->respond(new SlugNotSupportedError());

				$entity = $this->manager->getRepository($this->entityClass)->findOneBy([
					'slug' => $idOrSlug,
				]);
			}

			return $this->respond($entity);
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
					return $this->responder->createErrorResponse(new BadProjectionObjectError());
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
				return $this->responder->createNotFoundResponse();

			if (is_array($data))
				$data = $this->normalizeMany($data, $projection);
			else if ($data instanceof EntityInterface)
				$data = $projection->filter($this->normalizeOne($data, $projection));

			if ($data === null)
				$status = Response::HTTP_NO_CONTENT;
			else
				$status = Response::HTTP_OK;

			return new JsonResponse($data, $status, [
				'Cache-Control' => 'public, max-age=14400',
				'Content-Type' => 'application/json',
			]);
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