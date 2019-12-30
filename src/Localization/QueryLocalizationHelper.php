<?php
	namespace App\Localization;

	use DaybreakStudios\DoctrineQueryDocument\ResolverInterface;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\Mapping\ClassMetadata;
	use Doctrine\ORM\Query\Expr\Join;
	use Doctrine\ORM\QueryBuilder;
	use Symfony\Component\HttpFoundation\RequestStack;

	class QueryLocalizationHelper {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var RequestStack
		 */
		protected $requestStack;

		/**
		 * QueryLocalizationHelper constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param RequestStack           $requestStack
		 */
		public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack) {
			$this->entityManager = $entityManager;
			$this->requestStack = $requestStack;
		}

		/**
		 * @param ResolverInterface $resolver
		 * @param QueryBuilder      $qb
		 * @param array             $query
		 * @param array             $visited
		 * @param ClassMetadata[]   $entities
		 */
		public function addTranslationClauses(
			ResolverInterface $resolver,
			QueryBuilder $qb,
			array $query,
			array $visited = [],
			array $entities = []
		): void {
			if (!$entities) {
				$rootEntities = $qb->getRootEntities();

				foreach ($qb->getRootAliases() as $index => $rootAlias)
					$entities[$rootAlias] = $this->entityManager->getClassMetadata($rootEntities[$index]);

				foreach ($qb->getDQLPart('join') as $rootAlias => $joins) {
					/** @var Join $join */
					foreach ($joins as $join) {
						$parentMetadata = $entities[strtok($join->getJoin(), '.')];

						$entities[$join->getAlias()] = $this->entityManager->getClassMetadata(
							$parentMetadata->getAssociationTargetClass(strtok(''))
						);
					}
				}
			}

			foreach ($query as $key => $value) {
				if ($key[0] === '$') {
					$this->addTranslationClauses($resolver, $qb, $value, $visited, $entities);

					continue;
				}

				$alias = explode('.', $resolver->resolve($key))[0];

				if (!isset($entities[$alias]))
					continue;

				$metadata = $entities[$alias];
				$class = $metadata->getReflectionClass()->getName();

				if (!is_a($class, StringsEntityInterface::class, true) || isset($visited[$class]))
					continue;

				$visited[$class] = true;

				$qb
					->andWhere($alias . '.language = :language')
					->setParameter('language', $this->requestStack->getCurrentRequest()->getLocale());
			}
		}
	}