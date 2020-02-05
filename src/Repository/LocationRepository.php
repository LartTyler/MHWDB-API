<?php
	namespace App\Repository;

	use App\Entity\Location;
	use Doctrine\ORM\EntityRepository;

	class LocationRepository extends EntityRepository {
		/**
		 * @param string $language
		 * @param string $name
		 *
		 * @return Location|null
		 */
		public function findOneByName(string $language, string $name): ?Location {
			return $this->getEntityManager()->createQueryBuilder()
				->from(Location::class, 'l')
				->leftJoin('l.strings', 's')
				->select('l')
				->where('s.language = :language')
				->andWhere('s.name = :name')
				->setMaxResults(1)
				->setParameter('language', $language)
				->setParameter('name', $name)
				->getQuery()
				->getOneOrNullResult();
		}
	}