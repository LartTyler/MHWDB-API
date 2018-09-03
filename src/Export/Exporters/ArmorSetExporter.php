<?php
	namespace App\Export\Exporters;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonusRank;
	use App\Export\Export;

	class ArmorSetExporter extends AbstractExporter {
		/**
		 * ArmorSetExporter constructor.
		 */
		public function __construct() {
			parent::__construct(ArmorSet::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof ArmorSet))
				throw new \InvalidArgumentException('$object must be an instance of ' . ArmorSet::class);

			$output = [
				'name' => $object->getName(),
				'rank' => $object->getRank(),
				'pieces' => $object->getPieces()->map(function(Armor $armor): int {
					return $armor->getId();
				})->toArray(),
			];

			if ($bonus = $object->getBonus()) {
				$output['bonus'] = [
					'name' => $bonus->getName(),
					'ranks' => $bonus->getRanks()->map(function(ArmorSetBonusRank $rank): array {
						return [
							'pieces' => $rank->getPieces(),
							'skill' => $rank->getSkill()->getSkill()->getId(),
							'skillLevel' => $rank->getSkill()->getLevel(),
						];
					})->toArray(),
				];

				ksort($output['bonus']);
			}

			ksort($output);

			return new Export('armor-sets/' . $object->getRank(), $output);
		}
	}