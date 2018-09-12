<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\AilmentEntityData;
	use App\Entity\Ailment;
	use App\Export\Export;

	class AilmentExporter extends AbstractExporter {
		/**
		 * AilmentExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Ailment::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Ailment))
				throw new \InvalidArgumentException('$object must be an instance of ' . Ailment::class);

			$output = AilmentEntityData::fromEntity($object)->normalize();

			return new Export('ailments', $output);
		}
	}