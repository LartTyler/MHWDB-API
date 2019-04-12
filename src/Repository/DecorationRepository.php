<?php
	namespace App\Repository;

	use App\Entity\Decoration;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use Doctrine\ORM\EntityRepository;

	class DecorationRepository extends EntityRepository {
		/**
		 * @param Skill $skill
		 *
		 * @return Decoration[]
		 */
		public function findBySkill(Skill $skill): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(Decoration::class, 'd')
				->leftJoin('d.skills', 's')
				->select('d')
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
				->from(Decoration::class, 'd')
				->select('COUNT(d)')
				->where(':rank MEMBER OF d.skills')
				->setParameter('rank', $rank)
				->getQuery()
				->getSingleScalarResult();
		}
	}