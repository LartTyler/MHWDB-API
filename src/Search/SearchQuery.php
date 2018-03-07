<?php
	namespace App\Search;

	use App\Search\Exception\UnknownOperatorException;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\Query\Expr;
	use Doctrine\ORM\Query\Expr\Andx;
	use Doctrine\ORM\Query\Expr\Composite;
	use Doctrine\ORM\QueryBuilder;
	use Symfony\Bridge\Doctrine\RegistryInterface;

	class SearchQuery {
		/**
		 * @var SearchManager
		 */
		protected $searchManager;

		/**
		 * @var ObjectManager
		 */
		protected $entityManager;

		/**
		 * @var QueryBuilder
		 */
		protected $queryBuilder;

		/**
		 * @var FieldResolver
		 */
		protected $resolver;

		/**
		 * @var Andx
		 */
		protected $queryRoot;

		/**
		 * SearchQuery constructor.
		 *
		 * @param SearchManager $searchManager
		 * @param QueryBuilder  $queryBuilder
		 * @param ObjectManager $entityManager
		 */
		public function __construct(
			SearchManager $searchManager,
			QueryBuilder $queryBuilder,
			ObjectManager $entityManager
		) {
			$this->searchManager = $searchManager;
			$this->entityManager = $entityManager;
			$this->queryBuilder = $queryBuilder;

			$this->resolver = new FieldResolver($entityManager, $queryBuilder);
			$this->queryRoot = new Andx();

			$queryBuilder->where($this->queryRoot);
		}

		/**
		 * @param array          $query
		 * @param int            $paramIndex
		 *
		 * @param Composite|null $node
		 *
		 * @return void
		 * @throws Exception\CannotDirectlySearchAttributesException
		 * @throws Exception\CannotDirectlySearchRelationshipException
		 * @throws Exception\UnknownFieldException
		 * @throws UnknownOperatorException
		 */
		public function process(array $query, int &$paramIndex = 0, Composite $node = null): void {
			if (!$node)
				$node = $this->queryRoot;

			foreach ($query as $key => $value) {
				if (strpos($key, '$') === 0) {
					$operator = $this->searchManager->getOperator(substr($key, 1));

					if (!$operator)
						throw new UnknownOperatorException($key);

					$operator->process($this, $key, $value, $paramIndex, $node);
				} else {
					if (is_array($value)) {
						$itemKey = array_keys($value)[0];

						if (strpos($itemKey, '$') !== 0)
							throw new \InvalidArgumentException('You may only pass arrays as search values for ' .
								'operators');

						$operator = $this->searchManager->getOperator(substr($itemKey, 1));

						if (!$operator)
							throw new UnknownOperatorException($key);

						$operator->process($this, $key, array_values($value)[0], $paramIndex, $node);
					} else {
						$node->add($this->getResolver()->resolve($key) . ' = ?' . $paramIndex);

						$this->setParameter($paramIndex++, $value);
					}
				}
			}
		}

		/**
		 * @return FieldResolver
		 */
		public function getResolver(): FieldResolver {
			return $this->resolver;
		}

		/**
		 * @param string $key
		 *
		 * @return string
		 */
		public function resolveField(string $key): string {
			return $this->getResolver()->resolve($key);
		}

		public function expr(): Expr {
			return $this->queryBuilder->expr();
		}

		/**
		 * @param mixed $value
		 * @param int   $key
		 *
		 * @return $this
		 */
		public function setParameter(int $key, $value) {
			$this->queryBuilder->setParameter($key, $value);

			return $this;
		}
	}