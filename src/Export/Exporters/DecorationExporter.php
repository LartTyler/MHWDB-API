<?php
	namespace App\Export\Exporters;

	use App\Entity\Decoration;
	use App\Entity\SkillRank;
	use App\Export\Export;
	use App\Export\ExportHelper;

	class DecorationExporter extends AbstractExporter {
		/**
		 * DecorationExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Decoration::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Decoration))
				throw new \InvalidArgumentException('$object must be an instance of ' . Decoration::class);

			$output = [
				'slug' => $object->getSlug(),
				'name' => $object->getName(),
				'slot' => $object->getSlot(),
				'rarity' => $object->getRarity(),
				'skills' => $object->getSkills()->map(function(SkillRank $rank): array {
					return ExportHelper::toSimpleSkillRank($rank);
				})->toArray(),
			];

			ksort($output);

			return new Export('decorations', $output);
		}
	}