<?php
	namespace App\Import\Importers;

	use App\Contrib\EntityType;
	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ArmorSetImporter extends AbstractImporter {
		use EntityManagerAwareTrait;
		use ContribManagerAwareTrait;

		/**
		 * ArmorSetImporter constructor.
		 */
		public function __construct() {
			parent::__construct(ArmorSet::class);
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(object $data): EntityInterface {
			$armorSet = new ArmorSet($data->name, $data->rank);

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

			foreach ($data->pieces as $i => $armorId) {
				$armorId = $this->contribManager->getGroup(EntityType::ARMOR)->getTrueId($armorId);
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

				foreach ($definition->ranks as $i => $rankDefinition) {
					$skillId = $this->contribManager->getGroup(EntityType::SKILLS)
						->getTrueId($rankDefinition->skill->skill);

					/** @var Skill|null $skill */
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