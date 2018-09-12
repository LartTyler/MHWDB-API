<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\SkillEntityData;
	use App\Entity\Skill;
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

			$output = SkillEntityData::fromEntity($object)->normalize();

			return new Export('skills', $output);
		}
	}