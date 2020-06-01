<?php
	namespace App\Repository;

	use App\Entity\Quest;
	use Doctrine\ORM\EntityRepository;

	class QuestRepository extends EntityRepository {
		/**
		 * @param string $language
		 * @param string $name
		 *
		 * @return Quest|null
		 */
		public function findOneByName(string $language, string $name): ?Quest {
			return $this->getEntityManager()->createQueryBuilder()
				->from(Quest::class, 'quest')
				->leftJoin('quest.strings', 'strings')
				->select('quest')
				->where('strings.language = :language')
				->andWhere('strings.name = :name')
				->setMaxResults(1)
				->setParameter('language', $language)
				->setParameter('name', $name)
				->getQuery()
				->getOneOrNullResult();
		}
	}