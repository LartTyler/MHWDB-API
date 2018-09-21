<?php
	namespace App\Import\Importers;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Item;
	use App\Entity\Slot;
	use App\Entity\Weapon;
	use App\Entity\WeaponAssets;
	use App\Entity\WeaponCraftingInfo;
	use App\Entity\WeaponSharpness;
	use App\Import\AssetManager;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\ORM\EntityManagerInterface;

	class WeaponImporter extends AbstractImporter {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * @var AssetManager
		 */
		protected $assetManager;

		/**
		 * @var Weapon[]
		 */
		protected $weaponCache = [];

		/**
		 * @var Asset[]
		 */
		protected $assetCache = [];

		/**
		 * WeaponImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ContribManager         $contribManager
		 * @param AssetManager           $assetManager
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			ContribManager $contribManager,
			AssetManager $assetManager
		) {
			parent::__construct(Weapon::class);

			$this->entityManager = $entityManager;
			$this->contribManager = $contribManager;
			$this->assetManager = $assetManager;
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Weapon))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setSlug($data->slug)
				->setType($data->type)
				->setAttributes((array)$data->attributes);

			$entity->getSlots()->clear();

			foreach ($data->slots as $definition)
				$entity->getSlots()->add(new Slot($definition->rank));

			$entity->getDurability()->clear();

			foreach ($data->durability as $definition) {
				$sharpness = new WeaponSharpness();
				$sharpness
					->setRed($definition->red)
					->setOrange($definition->orange)
					->setYellow($definition->yellow)
					->setGreen($definition->green)
					->setBlue($definition->blue)
					->setWhite($definition->white);

				$entity->getDurability()->add($sharpness);
			}

			$elements = [];

			foreach ($data->elements as $definition) {
				$entity->setElement($definition->type, $definition->damage, $definition->hidden);

				$elements[] = $definition->type;
			}

			$removed = $entity->getElements()->matching(
				Criteria::create()
					->where(Criteria::expr()->notIn('type', $elements))
			);

			foreach ($removed as $item)
				$entity->getElements()->removeElement($item);

			$entity->getAttack()
				->setDisplay($data->attack->display)
				->setRaw($data->attack->raw);

			if ($definition = $data->crafting) {
				$crafting = $entity->getCrafting();

				if (!$crafting)
					$entity->setCrafting($crafting = new WeaponCraftingInfo($definition->craftable));
				else
					$crafting->setCraftable($definition->craftable);

				if ($definition->previous) {
					$previous = $this->getWeapon($definition->previous);

					if (!$previous) {
						throw $this->createMissingReferenceException(
							'crafting.previous',
							Weapon::class,
							$definition->previous
						);
					}

					$crafting->setPrevious($previous);

					if (!$previous->getCrafting()->getBranches()->contains($entity))
						$previous->getCrafting()->getBranches()->add($entity);
				} else if ($previous = $crafting->getPrevious()) {
					$previous->getCrafting()->getBranches()->removeElement($entity);

					$crafting->setPrevious(null);
				}

				$itemGroup = $this->contribManager->getGroup(EntityType::ITEMS);

				foreach (['crafting', 'upgrade'] as $type) {
					/** @var CraftingMaterialCost[]|Collection $collection */
					$collection = call_user_func([$crafting, 'get' . ucfirst($type) . 'Materials']);
					$collection->clear();

					foreach ($definition->{$type . 'Materials'} as $i => $cost) {
						$itemId = $itemGroup->getTrueId($cost->item);
						$item = $this->entityManager->getRepository(Item::class)->find($itemId);

						if (!$item) {
							throw $this->createMissingReferenceException(
								'crafting.' . $type . 'Materials[' . $i .
								'].item',
								Item::class,
								$itemId
							);
						}

						$collection->add(new CraftingMaterialCost($item, $cost->quantity));
					}
				}
			} else
				$entity->setCrafting(null);

			if ($definition = $data->assets) {
				$assets = $entity->getAssets();

				if (!$assets)
					$entity->setAssets($assets = new WeaponAssets(null, null));

				$weaponGroup = $this->contribManager->getGroup(EntityType::WEAPONS);

				foreach (['icon', 'image'] as $type) {
					$image = $definition->{$type};
					$setter = 'set' . ucfirst($type);

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
							$assetPath = $weaponGroup->getAssetPath($image->uri);

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
		}

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$weapon = new Weapon($data->name, $data->type, $data->rarity);
			$weapon->setId($id);

			$this->weaponCache[$id] = $weapon;

			$this->import($weapon, $data);

			return $weapon;
		}

		/**
		 * @param string|int $id
		 *
		 * @return Weapon|null
		 */
		protected function getWeapon($id): ?Weapon {
			if (isset($this->weaponCache[$id]))
				return $this->weaponCache[$id];

			return $this->weaponCache[$id] = $this->entityManager->getRepository(Weapon::class)->find($id);
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