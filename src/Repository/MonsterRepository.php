<?php
	namespace App\Repository;

	use App\Entity\Ailment;
	use App\Entity\Location;
	use App\Entity\Monster;
	use Doctrine\ORM\EntityRepository;

	class MonsterRepository extends EntityRepository {
		/**
		 * @param Ailment $ailment
		 *
		 * @return Monster[]
		 */
		public function findByAilment(Ailment $ailment): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(Monster::class, 'm')
				->leftJoin('m.ailments', 'a')
				->select('m')
				->where('a.id = :ailment')
				->setParameter('ailment', $ailment)
				->getQuery()
				->getResult();
		}

		/**
		 * @param Location $location
		 *
		 * @return Monster[]
		 */
		public function findByLocation(Location $location): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(Monster::class, 'm')
				->select('m')
				->where(':location MEMBER OF m.locations')
				->setParameter('location', $location)
				->getQuery()
				->getResult();
		}
	}