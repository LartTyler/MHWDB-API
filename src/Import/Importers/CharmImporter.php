<?php
	namespace App\Import\Importers;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\CharmRankCraftingInfo;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Item;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManagerInterface;

	class CharmImporter extends AbstractImporter {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * CharmImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ContribManager         $contribManager
		 */
		public function __construct(EntityManagerInterface $entityManager, ContribManager $contribManager) {
			parent::__construct(Charm::class);

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
			if (!($entity instanceof Charm))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setSlug($data->slug);

			$entity->getRanks()->clear();

			foreach ($data->ranks as $rankDefinition) {
				$rank = new CharmRank($entity, $rankDefinition->name, $rankDefinition->level);
				$rank->setRarity($rankDefinition->rarity);

				$skillGroup = $this->contribManager->getGroup(EntityType::SKILLS);

				foreach ($data->skills as $i => $skillDefinition) {
					$skillId = $skillGroup->getTrueId($skillDefinition->skill);
					$skill = $this->entityManager->getRepository(Skill::class)->find($skillId);

					if (!$skill) {
						throw $this->createMissingReferenceException(
							'skills[' . $i . '].skill',
							Skill::class,
							$skillId
						);
					}

					$skillRank = $skill->getRank($skillDefinition->level);

					if (!$skillRank) {
						throw $this->createMissingReferenceException(
							'skills[' . $i . '].level',
							SkillRank::class,
							$skillDefinition->level
						);
					}

					$rank->getSkills()->add($skillRank);
				}

				$rank->setCrafting($crafting = new CharmRankCraftingInfo($rankDefinition->craftable));

				$itemGroup = $this->contribManager->getGroup(EntityType::ITEMS);

				foreach ($rankDefinition->crafting->materials as $i => $costDefinition) {
					$itemId = $itemGroup->getTrueId($costDefinition->item);
					$item = $this->entityManager->getRepository(Item::class)->find($itemId);

					if (!$item) {
						throw $this->createMissingReferenceException(
							'crafting.materials[' . $i . '].item',
							Item::class,
							$itemId
						);
					}

					$crafting->getMaterials()->add(new CraftingMaterialCost($item, $costDefinition->quantity));
				}
			}
		}

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$charm = new Charm($data->name);
			$charm->setId($id);

			$this->import($charm, $data);

			return $charm;
		}
	}