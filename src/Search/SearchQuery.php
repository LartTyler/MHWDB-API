<?php
	namespace App\Search;

	use App\Search\Exception\UnknownOperatorException;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\Query\Expr\Andx;
	use Doctrine\ORM\Query\Expr\Base;
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
		 * @var Andx
		 */
		protected $queryRoot;

		/**
		 * @var int
		 */
		protected $paramIndex = 0;

		/**
		 * SearchQuery constructor.
		 *
		 * @param SearchManager     $searchManager
		 * @param RegistryInterface $registry
		 * @param QueryBuilder      $queryBuilder
		 */
		public function __construct(
			SearchManager $searchManager,
			RegistryInterface $registry,
			QueryBuilder $queryBuilder
		) {
			$this->searchManager = $searchManager;
			$this->entityManager = $registry->getManager();
			$this->queryBuilder = $queryBuilder;

			$this->queryRoot = new Andx();
		}

		/**
		 * @param array $query
		 *
		 * @return void
		 * @throws UnknownOperatorException
		 */
		public function process(array $query): void {
			$this->paramIndex = 0;

			foreach ($query as $key => $value) {
				if (strpos($key, '$') === 0) {
					$operator = $this->searchManager->getOperator(substr($key, 1));

					if (!$operator)
						throw new UnknownOperatorException($key);

					$this->andWhere($operator->process($this, $value, $paramIndex));
				}
			}
		}

		/**
		 * @param Base $node
		 *
		 * @return $this
		 */
		public function andWhere(Base $node) {
			$this->queryRoot->add($node);

			return $this;
		}

		/**
		 * @param string $key
		 * @param mixed  $value
		 *
		 * @return $this
		 */
		public function setParameter(string $key, $value) {
			$this->queryBuilder->setParameter($key . '_' . ++$this->paramIndex, $value);

			return $this;
		}

		public function resolveFieldName(string $field) {

		}
	}