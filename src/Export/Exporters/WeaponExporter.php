<?php
	namespace App\Export\Exporters;

	use App\Entity\Slot;
	use App\Entity\Weapon;
	use App\Entity\WeaponElement;
	use App\Entity\WeaponSharpness;
	use App\Export\Export;
	use App\Export\ExporterInterface;
	use App\Export\ExportHelper;
	use App\Game\Sharpness;

	class WeaponExporter implements ExporterInterface {
		/**
		 * @return string
		 */
		public function getSupportedClass(): string {
			return Weapon::class;
		}

		/**
		 * @param Weapon|object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Weapon))
				throw new \InvalidArgumentException('$object must be an instance of ' . Weapon::class);

			$output = [
				'slug' => $object->getSlug(),
				'attributes' => $object->getAttributes(),
				'name' => $object->getName(),
				'type' => $object->getType(),
				'rarity' => $object->getRarity(),
				'slots' => $object->getSlots()->map(function(Slot $slot): array {
					return [
						'rank' => $slot->getRank(),
					];
				})->toArray(),
				'durability' => $object->getDurability()->map(function(WeaponSharpness $sharpness): array {
					return [
						Sharpness::RED => $sharpness->getRed(),
						Sharpness::ORANGE => $sharpness->getOrange(),
						Sharpness::YELLOW => $sharpness->getYellow(),
						Sharpness::GREEN => $sharpness->getGreen(),
						Sharpness::BLUE => $sharpness->getBlue(),
						Sharpness::WHITE => $sharpness->getWhite(),
					];
				})->toArray(),
				'elements' => $object->getElements()->map(function(WeaponElement $element): array {
					return [
						'type' => $element->getType(),
						'damage' => $element->getDamage(),
						'hidden' => $element->isHidden(),
					];
				})->toArray(),
				'attack' => [
					'display' => $object->getAttack()->getDisplay(),
					'raw' => $object->getAttack()->getRaw(),
				],
				'crafting' => null,
				'assets' => null,
			];

			if ($crafting = $object->getCrafting()) {
				$output['crafting'] = [
					'craftable' => $crafting->isCraftable(),
					'previous' => $crafting->getPrevious() ? $crafting->getPrevious()->getId() : null,
					'branches' => $crafting->getBranches()->map(function(Weapon $weapon): int {
						return $weapon->getId();
					})->toArray(),
					'craftingMaterials' => ExportHelper::toSimpleCostArray($crafting->getCraftingMaterials()),
					'upgradeMaterials' => ExportHelper::toSimpleCostArray($crafting->getUpgradeMaterials()),
				];

				ksort($output['crafting']);
			}

			if ($assets = $object->getAssets()) {
				$output['assets'] = [
					'icon' => ExportHelper::toSimpleAsset($assets->getIcon()),
					'image' => ExportHelper::toSimpleAsset($assets->getImage()),
				];
			}

			ksort($output);

			return new Export('weapons/' . $object->getType(), $output);
		}
	}