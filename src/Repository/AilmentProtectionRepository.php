<?php
	namespace App\Repository;

	use App\Entity\AilmentProtection;
	use App\Entity\Item;
	use App\Entity\Skill;
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

		/**
		 * @param Skill $skill
		 *
		 * @return AilmentProtection[]
		 */
		public function findBySkill(Skill $skill): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(AilmentProtection::class, 'p')
				->select('p')
				->where(':skill MEMBER OF p.skills')
				->setParameter('skill', $skill)
				->getQuery()
				->getResult();
		}
	}