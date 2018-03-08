<?php
	namespace App\Controller;

	use App\Exceptions\JsonSearchOnNonJsonFieldException;
	use App\Entity\SluggableInterface;
	use App\Response\BadQueryObjectError;
	use App\Response\BadSearchParametersError;
	use App\Response\EmptySearchParametersError;
	use App\Response\SearchError;
	use App\Response\SlugNotSupportedError;
	use App\Search\SearchManager;
	use DaybreakStudios\DoctrineQueryDocument\QueryManager;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\DBAL\Types\Type;
	use Doctrine\ORM\EntityManager;
	use Doctrine\ORM\QueryBuilder;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\RouterInterface;

	abstract class AbstractDataController extends Controller {
		protected const SEARCH_OPERATORS = [
			'!' => '!=',
			'>=' => '>=',
			'<=' => '<=',
			'<' => '<',
			'>' => '>',
			'~' => 'LIKE',
			'!~' => 'NOT LIKE',
		];

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
		public function listAction(Request $request): Response {
			if ($request->query->has('q'))
				return $this->getSearchResults($request->query->all());

			$items = $this->manager->getRepository($this->entityClass)->findAll();

			return $this->responder->createResponse($items);
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return Response
		 */
		public function readAction(string $idOrSlug): Response {
			return $this->respond($this->getEntity($idOrSlug));
		}

		/**
		 * @param array $query
		 *
		 * @return Response
		 */
		protected function getSearchResults(array $query): Response {
			if ($limit = ($query['limit'] ?? null))
				unset($query['limit']);

			if ($offset = ($query['offset'] ?? null))
				unset($query['offset']);

			if (!sizeof($query))
				return $this->responder->createErrorResponse(new EmptySearchParametersError());

			$queryBuilder = $this->manager->createQueryBuilder()
				->from($this->entityClass, 'e')
				->select('e')
				->setMaxResults($limit)
				->setFirstResult($offset);

			$queryObject = $query['q'] ?? [];

			if (is_string($queryObject)) {
				$queryObject = json_decode($queryObject, true);

				if (json_last_error() !== JSON_ERROR_NONE)
					return $this->responder->createErrorResponse(new BadQueryObjectError());
			}

			if (!$queryObject)
				return $this->responder->createErrorResponse(new EmptySearchParametersError());

			try {
				$this->get(QueryManager::class)->apply($queryBuilder, $queryObject);
			} catch (\Exception $e) {
				return $this->responder->createErrorResponse(new SearchError($e->getMessage()));
			}

			return $this->respond($queryBuilder->getQuery()->getResult());
		}

		/**
		 * @param ApiErrorInterface|array|object|null $data
		 *
		 * @return Response
		 */
		protected function respond($data): Response {
			if ($data instanceof ApiErrorInterface)
				return $this->responder->createErrorResponse($data);
			else if ($data === null)
				return $this->responder->createNotFoundResponse();

			return $this->responder->createResponse($data);
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return SlugNotSupportedError|EntityInterface|null
		 */
		protected function getEntity(string $idOrSlug) {
			if (is_numeric($idOrSlug))
				$item = $this->manager->getRepository($this->entityClass)->find((int)$idOrSlug);
			else {
				if (!is_a($this->entityClass, SluggableInterface::class, true))
					return new SlugNotSupportedError();

				$item = $this->manager->getRepository($this->entityClass)->findOneBy([
					'slug' => $idOrSlug,
				]);
			}

			return $item;
		}
	}