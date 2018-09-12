<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\CharmEntityData;
	use App\Entity\Charm;
	use App\Export\Export;

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

			$output = CharmEntityData::fromEntity($object)->normalize();

			return new Export('charms', $output);
		}
	}