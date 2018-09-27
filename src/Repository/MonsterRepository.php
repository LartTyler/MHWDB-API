<?php
	namespace App\Repository;

	use App\Entity\Ailment;
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
	}