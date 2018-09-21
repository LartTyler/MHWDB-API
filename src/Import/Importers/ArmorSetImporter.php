<?php
	namespace App\Import\Importers;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManagerInterface;

	class ArmorSetImporter extends AbstractImporter {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * ArmorSetImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ContribManager         $contribManager
		 */
		public function __construct(EntityManagerInterface $entityManager, ContribManager $contribManager) {
			parent::__construct(ArmorSet::class);

			$this->entityManager = $entityManager;
			$this->contribManager = $contribManager;
		}

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$armorSet = new ArmorSet($data->name, $data->rank);
			$armorSet->setId($id);

			$this->import($armorSet, $data);

			return $armorSet;
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof ArmorSet))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setRank($data->rank);

			$entity->getPieces()->clear();

			$armorGroup = $this->contribManager->getGroup(EntityType::ARMOR);

			foreach ($data->pieces as $i => $armorId) {
				$armorId = $armorGroup->getTrueId($armorId);
				$armor = $this->entityManager->getRepository(Armor::class)->find($armorId);

				if (!$armor)
					throw $this->createMissingReferenceException('pieces[' . $i . ']', Armor::class, $armorId);

				$entity->getPieces()->add($armor);
				$armor->setArmorSet($entity);
			}

			if ($definition = $data->bonus) {
				$bonus = $entity->getBonus();

				if (!$bonus) {
					$bonus = new ArmorSetBonus($definition->name);

					$entity->setBonus($bonus);
				}

				$bonus->getRanks()->clear();

				$skillGroup = $this->contribManager->getGroup(EntityType::SKILLS);

				foreach ($definition->ranks as $i => $rankDefinition) {
					$skillId = $skillGroup->getTrueId($rankDefinition->skill->skill);
					$skill = $this->entityManager->getRepository(Skill::class)->find($skillId);

					if (!$skillId) {
						throw $this->createMissingReferenceException('bonus.ranks[' . $i . '].skill.skill',
							Skill::class, $skillId);
					}

					$skillRank = $skill->getRank($rankDefinition->skill->level);

					if (!$skillRank) {
						throw $this->createMissingReferenceException('bonus.ranks[' . $i . '].skill.level',
							SkillRank::class, $rankDefinition->skill->level);
					}

					$bonus->getRanks()->add(new ArmorSetBonusRank($bonus, $rankDefinition->pieces, $skillRank));
				}
			} else
				$entity->setBonus(null);
		}
	}