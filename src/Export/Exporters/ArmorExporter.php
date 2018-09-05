<?php
	namespace App\Export\Exporters;

	use App\Entity\Armor;
	use App\Entity\SkillRank;
	use App\Entity\Slot;
	use App\Export\AssetExport;
	use App\Export\Export;
	use App\Export\ExportHelper;
	use App\Game\Element;

	class ArmorExporter extends AbstractExporter {
		/**
		 * ArmorExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Armor::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Armor))
				throw new \InvalidArgumentException('$object must be an instance of ' . Armor::class);

			$resistances = $object->getResistances();
			$defense = $object->getDefense();

			$output = [
				'slug' => $object->getSlug(),
				'attributes' => $object->getAttributes(),
				'name' => $object->getName(),
				'type' => $object->getType(),
				'rank' => $object->getRank(),
				'rarity' => $object->getRarity(),
				'resistances' => [
					Element::FIRE => $resistances->getFire(),
					Element::WATER => $resistances->getWater(),
					Element::ICE => $resistances->getIce(),
					Element::THUNDER => $resistances->getThunder(),
					Element::DRAGON => $resistances->getDragon(),
				],
				'defense' => [
					'base' => $defense->getBase(),
					'max' => $defense->getMax(),
					'augmented' => $defense->getAugmented(),
				],
				'skills' => $object->getSkills()->map(function(SkillRank $rank): array {
					return ExportHelper::toSimpleSkillRank($rank);
				})->toArray(),
				'slots' => $object->getSlots()->map(function(Slot $slot): array {
					return [
						'rank' => $slot->getRank(),
					];
				})->toArray(),
				'armorSet' => ExportHelper::toReference($object->getArmorSet()),
			];

			/** @var AssetExport[] $assetExports */
			$assetExports = [];

			if ($assets = $object->getAssets()) {
				if ($imageFemale = $assets->getImageFemale())
					$assetExports[] = AssetExport::fromAsset($imageFemale);

				if ($imageMale = $assets->getImageMale())
					$assetExports[] = AssetExport::fromAsset($imageMale);

				$output['assets'] = [
					'imageFemale' => ExportHelper::toSimpleAsset($imageFemale),
					'imageMale' => ExportHelper::toSimpleAsset($imageMale),
				];
			}

			if ($crafting = $object->getCrafting()) {
				$output['crafting'] = [
					'materials' => ExportHelper::toSimpleCostArray($crafting->getMaterials()),
				];
			}

			ksort($output);

			$export = new Export('armor/' . $object->getRank(), $output);
			$export->setAssets($assetExports);

			return $export;
		}
	}