<?php
	namespace App\Repository;

	use App\Entity\AilmentProtection;
	use App\Entity\Item;
	use Doctrine\ORM\EntityRepository;

	class AilmentProtectionRepository extends EntityRepository {
		/**
		 * @param Item $item
		 *
		 * @return AilmentProtection[]
		 */
		public function findByItem(Item $item): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(AilmentProtection::class, 'p')
				->select('p')
				->where(':item MEMBER OF p.items')
				->setParameter('item', $item)
				->getQuery()
				->getResult();
		}
	}