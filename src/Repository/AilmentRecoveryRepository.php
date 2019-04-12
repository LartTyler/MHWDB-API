<?php
	namespace App\Repository;

	use App\Entity\AilmentRecovery;
	use App\Entity\Item;
	use Doctrine\ORM\EntityRepository;

	class AilmentRecoveryRepository extends EntityRepository {
		/**
		 * @param Item $item
		 *
		 * @return AilmentRecovery[]
		 */
		public function findByItem(Item $item): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(AilmentRecovery::class, 'r')
				->select('r')
				->where(':item MEMBER OF r.items')
				->setParameter('item', $item)
				->getQuery()
				->getResult();
		}
	}