<?php
	namespace App\Export\Exporters;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonusRank;
	use App\Export\Export;
	use App\Export\ExportHelper;

	class ArmorSetExporter extends AbstractExporter {
		/**
		 * @var ExportHelper
		 */
		protected $helper;

		/**
		 * ArmorSetExporter constructor.
		 *
		 * @param ExportHelper $helper
		 */
		public function __construct(ExportHelper $helper) {
			parent::__construct(ArmorSet::class);

			$this->helper = $helper;
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof ArmorSet))
				throw new \InvalidArgumentException('$object must be an instance of ' . ArmorSet::class);

			$helper = $this->helper;

			$output = [
				'name' => $object->getName(),
				'rank' => $object->getRank(),
				'pieces' => $object->getPieces()->map(function(Armor $armor) use ($helper): string {
					return $helper->getReference($armor, 'armor.read', 'idOrSlug');
				})->toArray(),
			];

			if ($bonus = $object->getBonus()) {
				$output['bonus'] = [
					'name' => $bonus->getName(),
					'ranks' => $bonus->getRanks()->map(function(ArmorSetBonusRank $rank) use ($helper): array {
						return [
							'pieces' => $rank->getPieces(),
							'skill' => $helper->toSimpleSkillRank($rank->getSkill()),
						];
					})->toArray(),
				];

				ksort($output['bonus']);
			}

			ksort($output);

			return new Export('armor-sets/' . $object->getRank(), $output);
		}
	}