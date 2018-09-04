<?php
	namespace App\Export\Exporters;

	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Export\Export;

	class SkillExporter extends AbstractExporter {
		/**
		 * SkillExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Skill::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Skill))
				throw new \InvalidArgumentException('$object must be an instance of ' . Skill::class);

			$output = [
				'slug' => $object->getSlug(),
				'name' => $object->getName(),
				'ranks' => $object->getRanks()->map(function(SkillRank $rank): array {
					$output = [
						'slug' => $rank->getSlug(),
						'level' => $rank->getLevel(),
						'description' => $rank->getDescription(),
						'modifiers' => $rank->getModifiers(),
					];

					ksort($output);

					return $output;
				})->toArray(),
				'description' => $object->getDescription(),
			];

			ksort($output);

			return new Export('skills', $output);
		}
	}