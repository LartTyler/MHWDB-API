<?php
	namespace App\Repository;

	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use Doctrine\ORM\EntityRepository;

	class ArmorSetBonusRankRepository extends EntityRepository {
		/**
		 * @param Skill $skill
		 *
		 * @return ArmorSetBonusRank[]
		 */
		public function findBySkill(Skill $skill): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(ArmorSetBonusRank::class, 'b')
				->leftJoin('b.skill', 's')
				->select('b')
				->where('s.skill = :skill')
				->setParameter('skill', $skill)
				->getQuery()
				->getResult();
		}

		/**
		 * @param SkillRank $rank
		 *
		 * @return int
		 */
		public function countBySkillRank(SkillRank $rank): int {
			return (int)$this->getEntityManager()->createQueryBuilder()
				->from(ArmorSetBonusRank::class, 'b')
				->select('COUNT(b)')
				->where('b.skill = :rank')
				->setParameter('rank', $rank)
				->getQuery()
				->getSingleScalarResult();
		}
	}