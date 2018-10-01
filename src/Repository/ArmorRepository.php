<?php
	namespace App\Repository;

	use App\Entity\Armor;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use Doctrine\ORM\EntityRepository;

	class ArmorRepository extends EntityRepository {
		/**
		 * @param Skill $skill
		 *
		 * @return Armor[]
		 */
		public function findBySkill(Skill $skill): array {
			return $this->getEntityManager()->createQueryBuilder()
				->from(Armor::class, 'a')
				->leftJoin('a.skills', 's')
				->select('a')
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
				->from(Armor::class, 'a')
				->select('COUNT(a)')
				->where(':rank MEMBER OF a.skills')
				->setParameter('rank', $rank)
				->getQuery()
				->getSingleScalarResult();
		}
	}