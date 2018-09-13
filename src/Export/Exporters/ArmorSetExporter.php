<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\ArmorSetEntityData;
	use App\Entity\ArmorSet;
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

			$data = ArmorSetEntityData::fromEntity($object);

			return new Export($data->getEntityGroupName(), $data->normalize());
		}
	}