<?php
	namespace App\Export\Exporters;

	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\SkillRank;
	use App\Export\Export;
	use App\Export\ExportHelper;

	class CharmExporter extends AbstractExporter {
		/**
		 * CharmExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Charm::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Charm))
				throw new \InvalidArgumentException('$object must be an instance of ' . Charm::class);

			$output = [
				'slug' => $object->getSlug(),
				'name' => $object->getName(),
				'ranks' => $object->getRanks()->map(function(CharmRank $rank): array {
					$output = [
						'name' => $rank->getName(),
						'level' => $rank->getLevel(),
						'rarity' => $rank->getRarity(),
						'skills' => $rank->getSkills()->map(function(SkillRank $rank): array {
							return ExportHelper::toSimpleSkillRank($rank);
						})->toArray(),
					];

					if ($crafting = $rank->getCrafting()) {
						$output['crafting'] = [
							'craftable' => $crafting->isCraftable(),
							'materials' => ExportHelper::toSimpleCostArray($crafting->getMaterials()),
						];
					}

					ksort($output);

					return $output;
				})->toArray(),
			];

			ksort($output);

			return new Export('charms', $output);
		}
	}