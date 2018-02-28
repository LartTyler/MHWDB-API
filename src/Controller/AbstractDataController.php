<?php
	namespace App\Controller;

	use App\Exceptions\JsonSearchOnNonJsonFieldException;
	use App\Entity\SluggableInterface;
	use App\Response\BadSearchParametersError;
	use App\Response\EmptySearchParametersError;
	use App\Response\SlugNotSupportedError;
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
		 * @return Response
		 */
		public function listAction(): Response {
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
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function searchAction(Request $request): Response {
			return $this->getSearchResults($request->query->all());
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

			$qb = $this->manager->createQueryBuilder()
				->from($this->entityClass, $alias = 'e')
				->select($alias)
				->setMaxResults($limit)
				->setFirstResult($offset);

			foreach ($query as $field => $value)
				try {
					$this->addSearchFieldClauses($qb, $alias, $field, $value);
				} catch (JsonSearchOnNonJsonFieldException $e) {
					return $this->responder->createErrorResponse(new BadSearchParametersError($e->getMessage()));
				}

			return $this->respond($qb->getQuery()->getResult());
		}

		/**
		 * @param QueryBuilder $qb
		 * @param string       $alias
		 * @param string       $field
		 * @param              $value
		 *
		 * @return void
		 */
		protected function addSearchFieldClauses(QueryBuilder $qb, string $alias, string $field, $value): void {
			$parts = array_map(function(string $item): string {
				return trim($item);
			}, explode(',', $value));

			foreach ($parts as $i => $part) {
				$operator = '=';

				foreach (self::SEARCH_OPERATORS as $symbol => $op) {
					if (strpos($part, $symbol) !== 0)
						continue;

					$operator = $op;
					$part = substr($part, strlen($symbol));

					break;
				}

				// For searching JSON attributes, in the form 'field_attribute'
				if (strpos($field, '_')) {
					$actualField = strtok($field, '_');

					if (!$this->isJsonField($actualField))
						throw new JsonSearchOnNonJsonFieldException($actualField);

					$fieldKey = sprintf("JSON_UNQUOTE(JSON_EXTRACT(%s.%s, '$.%s'))", $alias, $actualField, strtok(''));
				} else
					$fieldKey = sprintf('%s.%s', $alias, $field);

				$paramKey = sprintf('%s_%d', $field, $i);

				$qb
					// sprintf("{alias}.{field} {operator} :{paramKey}", ...)
					->andWhere($_ = sprintf('%s %s :%s', $fieldKey, $operator, $paramKey))
					->setParameter($paramKey, $part);
			}
		}

		/**
		 * @param string $field
		 *
		 * @return bool
		 */
		protected function isJsonField(string $field): bool {
			return $this->manager->getClassMetadata($this->entityClass)->getTypeOfField($field) === Type::JSON;
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