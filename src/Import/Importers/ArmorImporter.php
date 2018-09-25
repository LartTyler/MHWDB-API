<?php
	namespace App\Import\Importers;

	use App\Contrib\Delete\DeleteResult;
	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
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
	use App\Import\AssetManager;
	use App\Import\ManagedDeleteInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManager;
	use Doctrine\ORM\EntityManagerInterface;

	class ArmorImporter extends AbstractImporter implements ManagedDeleteInterface {
		/**
		 * @var EntityManager
		 */
		protected $entityManager;

		/**
		 * @var AssetManager
		 */
		protected $assetManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * @var Asset[]
		 */
		protected $assetCache = [];

		/**
		 * ArmorImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param AssetManager           $assetManager
		 * @param ContribManager         $contribManager
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			AssetManager $assetManager,
			ContribManager $contribManager
		) {
			parent::__construct(Armor::class);

			$this->entityManager = $entityManager;
			$this->assetManager = $assetManager;
			$this->contribManager = $contribManager;
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

			$skillGroup = $this->contribManager->getGroup(EntityType::SKILLS);

			foreach ($data->skills as $i => $definition) {
				$skillId = $skillGroup->getTrueId($defense->skill);
				$skill = $this->entityManager->getRepository(Skill::class)->find($skillId);

				if (!$skill)
					throw $this->createMissingReferenceException('skills[' . $i . '].skill', Skill::class, $skillId);

				$rank = $skill->getRank($definition->level);

				if (!$rank) {
					throw $this->createMissingReferenceException(
						'skills[' . $i . '].level',
						SkillRank::class,
						$definition->level
					);
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
				}

				foreach (Gender::ALL as $gender) {
					$key = 'image' . ucfirst($gender);
					$image = $definition->{$key};

					$setter = 'set' . ucfirst($key);

					if ($image) {
						$asset = $this->getAsset($image->primaryHash, $image->secondaryHash);

						if (!$asset) {
							$asset = new Asset(
								$this->assetManager->toBucketUri($image->uri),
								$image->primaryHash,
								$image->secondaryHash
							);

							$this->assetCache[$image->primaryHash . '.' . $image->secondaryHash] = $asset;
						}

						call_user_func([$assets, $setter], $asset);

						if (!$asset->getId()) {
							$assetPath = $this->contribManager->getGroup(EntityType::ARMOR)->getAssetPath($image->uri);

							if (!$assetPath)
								throw $this->createAssetNotFoundException($image->uri);

							$handle = fopen($assetPath, 'r');

							$this->assetManager->put(ltrim($image->uri, '/'), $handle);
						}
					} else
						call_user_func([$assets, $setter], null);
				}
			} else
				$entity->setAssets(null);

			if ($definition = $data->crafting) {
				$crafting = $entity->getCrafting();

				if (!$crafting)
					$entity->setCrafting($crafting = new ArmorCraftingInfo());

				$crafting->getMaterials()->clear();

				foreach ($definition->materials as $i => $cost) {
					$item = $this->entityManager->getRepository(Item::class)->find($cost->item);

					if (!$item) {
						throw $this->createMissingReferenceException(
							'crafting.materials[' . $i . '].item',
							Item::class,
							$cost->item
						);
					}

					$crafting->getMaterials()->add(new CraftingMaterialCost($item, $cost->quantity));
				}
			} else
				$entity->setCrafting(null);
		}

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$armor = new Armor($data->name, $data->type, $data->rank, $data->rarity);
			$armor->setId($id);

			$this->import($armor, $data);

			return $armor;
		}

		/**
		 * @param EntityInterface|Armor $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void {
			$assets = $entity->getAssets();

			if (!$assets)
				return;

			if ($asset = $assets->getImageMale())
				$this->assetManager->deleteUri($asset->getUri());

			if ($asset = $assets->getImageFemale())
				$this->assetManager->deleteUri($asset->getUri());
		}

		/**
		 * @param string $primaryHash
		 * @param string $secondaryHash
		 *
		 * @return Asset|null
		 */
		protected function getAsset(string $primaryHash, string $secondaryHash): ?Asset {
			$key = $primaryHash . '.' . $secondaryHash;

			if (isset($this->assetCache[$key]))
				return $this->assetCache[$key];

			return $this->assetCache[$key] = $this->entityManager->getRepository(Asset::class)->findOneBy(
				[
					'primaryHash' => $primaryHash,
					'secondaryHash' => $secondaryHash,
				]
			);
		}
	}