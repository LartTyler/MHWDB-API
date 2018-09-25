<?php
	namespace App\Import\Importers;

	use App\Contrib\Management\ContribManager;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Import\ManagedDeleteInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManagerInterface;

	class ArmorSetBonusImporter extends AbstractImporter implements ManagedDeleteInterface {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * ArmorSetBonusImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ContribManager         $contribManager
		 */
		public function __construct(EntityManagerInterface $entityManager, ContribManager $contribManager) {
			parent::__construct(ArmorSetBonus::class);

			$this->entityManager = $entityManager;
			$this->contribManager = $contribManager;
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof ArmorSetBonus))
				throw $this->createCannotImportException();

			$entity->setName($data->name);

			$entity->getRanks()->clear();

			foreach ($data->ranks as $i => $defintion) {
				$skill = $this->entityManager->getRepository(Skill::class)->find($skillId = $defintion->skill->skill);

				if (!$skill) {
					throw $this->createMissingReferenceException(
						'ranks[' . $i . '].skill.skill',
						Skill::class,
						$skillId
					);
				}

				$skillRank = $skill->getRank($defintion->skill->level);

				if (!$skillRank) {
					throw $this->createMissingReferenceException(
						'ranks[' . $i . '].skill.level',
						SkillRank::class,
						$defintion->skill->level
					);
				}

				$entity->getRanks()->add(new ArmorSetBonusRank($entity, $defintion->pieces, $skillRank));
			}
		}

		/**
		 * @param int|null $id
		 * @param object   $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$bonus = new ArmorSetBonus($data->name);
			$bonus->setId($id);

			$this->import($bonus, $data);

			return $bonus;
		}

		/**
		 * @param EntityInterface|ArmorSetBonus $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void {
			/** @var ArmorSet[] $armorSets */
			$armorSets = $this->entityManager->createQueryBuilder()
				->from(ArmorSet::class, 's')
				->select('s')
				->where('IDENTITY(s.bonus) = :bonus')
				->setParameter('bonus', $entity)
				->getQuery()
				->getResult();

			foreach ($armorSets as $armorSet)
				$armorSet->setBonus(null);
		}
	}