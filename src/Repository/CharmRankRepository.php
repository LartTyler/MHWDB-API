<?php
	namespace App\Repository;

	use App\Entity\CharmRank;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use Doctrine\ORM\EntityRepository;

	class CharmRankRepository extends EntityRepository {
		/**
		 * @param Skill $skill
		 *
		 * @return CharmRank[]
		 */
		public function findBySkill(Skill $skill): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(CharmRank::class, 'c')
				->leftJoin('c.skills', 's')
				->select('c')
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
				->from(CharmRank::class, 'c')
				->select('COUNT(c)')
				->where(':rank MEMBER OF c.skills')
				->setParameter('rank', $rank)
				->getQuery()
				->getSingleScalarResult();
		}
	}