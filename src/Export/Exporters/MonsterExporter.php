<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\MonsterEntityData;
	use App\Entity\Monster;
	use App\Export\Export;

	class MonsterExporter extends AbstractExporter {
		/**
		 * MonsterExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Monster::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Monster))
				throw new \InvalidArgumentException('$object must be an instance of ' . Monster::class);

			$output = MonsterEntityData::fromEntity($object)->normalize();

			return new Export('monsters', $output);
		}
	}