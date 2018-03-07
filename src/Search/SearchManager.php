<?php
	namespace App\Search;

	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\QueryBuilder;
	use Symfony\Bridge\Doctrine\RegistryInterface;

	class SearchManager {
		protected $operators;

		/**
		 * SearchManager constructor.
		 *
		 * @param OperatorInterface[] $operators
		 */
		public function __construct(array $operators = []) {
			$this->operators = $operators;
		}

		/**
		 * @return OperatorInterface[]
		 */
		public function getOperators(): array {
			return $this->operators;
		}

		/**
		 * @param string $key
		 *
		 * @return OperatorInterface|null
		 */
		public function getOperator(string $key): ?OperatorInterface {
			if (isset($this->operators[$key]))
				return $this->operators[$key];

			return null;
		}

		/**
		 * @param string            $key
		 * @param OperatorInterface $operator
		 *
		 * @return $this
		 */
		public function setOperator(string $key, OperatorInterface $operator) {
			$this->operators[$key] = $operator;

			return $this;
		}

		/**
		 * @param string $key
		 *
		 * @return $this
		 */
		public function removeOperator(string $key) {
			unset($this->operators[$key]);

			return $this;
		}

		/**
		 * @param ObjectManager $entityManager
		 * @param QueryBuilder  $queryBuilder
		 *
		 * @return SearchQuery
		 */
		public function create(ObjectManager $entityManager, QueryBuilder $queryBuilder): SearchQuery {
			return new SearchQuery($this, $queryBuilder, $entityManager);
		}
	}