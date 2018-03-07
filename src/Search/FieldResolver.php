<?php
	namespace App\Search;

	use App\Search\Exception\CannotDirectlySearchAttributesException;
	use App\Search\Exception\CannotDirectlySearchRelationshipException;
	use App\Search\Exception\SearchException;
	use App\Search\Exception\UnknownFieldException;
	use Doctrine\Common\Persistence\Mapping\ClassMetadata;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\DBAL\Types\Type;
	use Doctrine\ORM\QueryBuilder;
	use Symfony\Bridge\Doctrine\RegistryInterface;

	class FieldResolver {
		/**
		 * @var \Doctrine\Common\Persistence\ObjectManager
		 */
		protected $entityManager;

		/**
		 * @var QueryBuilder
		 */
		protected $queryBuilder;

		/**
		 * @var ClassMetadata
		 */
		protected $rootMetadata;

		/**
		 * @var string
		 */
		protected $rootAlias;

		/**
		 * @var string[]
		 */
		protected $joins = [];

		/**
		 * @var array
		 */
		protected $resolveCache = [];

		/**
		 * FieldResolver constructor.
		 *
		 * @param ObjectManager $entityManager
		 * @param QueryBuilder  $queryBuilder
		 */
		public function __construct(ObjectManager $entityManager, QueryBuilder $queryBuilder) {
			$this->entityManager = $entityManager;
			$this->queryBuilder = $queryBuilder;

			$this->rootMetadata = $this->entityManager->getClassMetadata($queryBuilder->getRootEntities()[0]);
			$this->rootAlias = $queryBuilder->getRootAliases()[0];
		}

		/**
		 * @param string $field
		 *
		 * @return string
		 * @throws SearchException
		 */
		public function resolve(string $field): string {
			if (isset($this->resolveCache[$field]))
				return $this->resolveCache[$field];

			$parts = explode('.', $field);
			$actualField = array_pop($parts);

			if (!sizeof($parts)) {
				if ($this->rootMetadata->hasAssociation($actualField))
					throw new CannotDirectlySearchRelationshipException($actualField);

				return $this->rootAlias . '.' . $actualField;
			}

			// skills.level
			// skills.skill.name

			$metadata = $this->rootMetadata;
			$alias = $this->rootAlias;

			foreach ($parts as $i => $part) {
				if ($metadata->getTypeOfField($part) === Type::JSON) {
					if (isset($parts[$i + 1]))
						$items = array_slice($parts, $i + 1);
					else
						$items = [];

					$items[] = $actualField;

					$jsonKey = implode('.', $items);

					return sprintf("JSON_UNQUOTE(JSON_EXTRACT(%s.%s, '\$.%s'))", $alias, $part, $jsonKey);
				} else if (!$metadata->hasAssociation($part))
					throw new UnknownFieldException($field);

				$metadata = $this->entityManager->getClassMetadata($metadata->getAssociationTargetClass($part));
				$alias = $this->getJoinAlias($alias, $part);
			}

			if (!$metadata->hasField($actualField))
				throw new UnknownFieldException($field);

			return $alias . '.' . $actualField;
		}

		/**
		 * @param string $parentAlias
		 * @param string $parentField
		 *
		 * @return string
		 */
		protected function getJoinAlias(string $parentAlias, string $parentField): string {
			$joinKey = $parentAlias . '.' . $parentField;

			if (isset($this->joins[$joinKey]))
				return $this->joins[$joinKey];

			$alias = 'join_' . sizeof($this->joins);

			$this->queryBuilder->leftJoin($joinKey, $alias);

			return $this->joins[$joinKey] = $alias;
		}
	}