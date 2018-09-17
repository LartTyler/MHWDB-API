<?php
	namespace App\Import\Importers;

	use App\Contrib\EntityType;
	use App\Entity\Armor;
	use App\Entity\ArmorAssets;
	use App\Entity\ArmorCraftingInfo;
	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Item;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Entity\Slot;
	use App\Game\Gender;
	use App\Import\ManagedDeleteInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ArmorImporter extends AbstractImporter implements ManagedDeleteInterface {
		use EntityManagerAwareTrait;
		use AssetManagerAwareTrait;
		use ContribManagerAwareTrait;

		/**
		 * ArmorImporter constructor.
		 */
		public function __construct() {
			parent::__construct(Armor::class);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Armor))
				throw $this->createCannotImportException();

			$entity
				->setSlug($data->slug)
				->setName($data->name)
				->setAttributes((array)$data->attributes)
				->setType($data->type)
				->setRank($data->rank)
				->setRarity($data->rarity);

			$resistances = $entity->getResistances();
			$resistances
				->setFire($data->resistances->fire)
				->setWater($data->resistances->water)
				->setIce($data->resistances->ice)
				->setThunder($data->resistances->thunder)
				->setDragon($data->resistances->dragon);

			$defense = $entity->getDefense();
			$defense
				->setBase($data->defense->base)
				->setMax($data->defense->max)
				->setAugmented($data->defense->augmented);

			$entity->getSkills()->clear();

			foreach ($data->skills as $i => $definition) {
				$skill = $this->entityManager->getRepository(Skill::class)->find($definition->skill);

				if (!$skill) {
					throw $this->createMissingReferenceException('skills[' . $i . '].skill', Skill::class,
						$definition->skill);
				}

				$rank = $skill->getRank($definition->level);

				if (!$rank) {
					throw $this->createMissingReferenceException('skills[' . $i . '].level', SkillRank::class,
						$definition->level);
				}

				$entity->getSkills()->add($rank);
			}

			$entity->getSlots()->clear();

			foreach ($data->slots as $definition)
				$entity->getSlots()->add(new Slot($definition->rank));

			if ($definition = $data->assets) {
				$assets = $entity->getAssets();

				if (!$assets) {
					$assets = new ArmorAssets(null, null);

					$entity->setAssets($assets);
				} else {
					$assets
						->setImageMale(null)
						->setImageFemale(null);
				}

				foreach (Gender::ALL as $gender) {
					$key = 'image' . ucfirst($gender);
					$setter = 'set' . ucfirst($key);

					$image = $definition->{$key};

					if ($image) {
						$asset = new Asset($image->uri, $image->primaryHash, $image->secondaryHash);

						call_user_func([$assets, $setter], $asset);

						$assetPath = $this->contribManager->getGroup(EntityType::ARMOR)->getAssetPath($image->uri);

						if (!$assetPath)
							throw $this->createAssetNotFoundException($image->uri);

						$handle = fopen($assetPath, 'r');

						$this->assetManager->put(ltrim(parse_url($image->uri, PHP_URL_PATH), '/'), $handle);
					} else
						call_user_func([$assets, $setter], null);
				}
			} else if ($assets = $entity->getAssets()) {
				if ($asset = $assets->getImageMale())
					$this->assetManager->deleteUri($asset->getUri());

				if ($asset = $assets->getImageFemale())
					$this->assetManager->deleteUri($asset->getUri());

				$assets
					->setImageMale(null)
					->setImageFemale(null);
			}

			if ($definition = $data->crafting) {
				$crafting = $entity->getCrafting();

				if (!$crafting)
					$entity->setCrafting($crafting = new ArmorCraftingInfo());

				$crafting->getMaterials()->clear();

				foreach ($definition->materials as $i => $cost) {
					$item = $this->entityManager->getRepository(Item::class)->find($cost->item);

					if (!$item) {
						throw $this->createMissingReferenceException('crafting.materials[' . $i . '].item', Item::class,
							$cost->item);
					}

					$crafting->getMaterials()->add(new CraftingMaterialCost($item, $cost->quantity));
				}
			} else
				$entity->setCrafting(null);
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(object $data): EntityInterface {
			$armor = new Armor($data->name, $data->type, $data->rank, $data->rarity);

			$this->import($armor, $data);

			return $armor;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void {
			if (!($entity instanceof Armor))
				throw $this->createCannotImportException();

			$assets = $entity->getAssets();

			if (!$assets)
				return;

			if ($asset = $assets->getImageMale())
				$this->assetManager->deleteUri($asset->getUri());

			if ($asset = $assets->getImageFemale())
				$this->assetManager->deleteUri($asset->getUri());
		}
	}