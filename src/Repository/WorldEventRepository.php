<?php
	namespace App\Repository;

	use App\Entity\WorldEvent;
	use Doctrine\ORM\EntityRepository;

	class WorldEventRepository extends EntityRepository {
		/**
		 * @param string             $language
		 * @param string             $name
		 * @param string             $platform
		 * @param \DateTimeInterface $startTimestamp
		 *
		 * @return WorldEvent|null
		 */
		public function search(
			string $language,
			string $name,
			string $platform,
			\DateTimeInterface $startTimestamp
		): ?WorldEvent {
			return $this->getEntityManager()->createQueryBuilder()
				->from(WorldEvent::class, 'e')
				->leftJoin('e.strings', 's')
				->select('e')
				->where('s.language = :language')
				->andWhere('s.name = :name')
				->andWhere('e.platform = :platform')
				->andWhere('e.startTimestamp = :start')
				->setMaxResults(1)
				->setParameter('language', $language)
				->setParameter('name', $name)
				->setParameter('platform', $platform)
				->setParameter('start', $startTimestamp)
				->getQuery()
				->getOneOrNullResult();
		}
	}