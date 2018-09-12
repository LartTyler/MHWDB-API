<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\LocationEntityData;
	use App\Entity\Location;
	use App\Export\Export;

	class LocationExporter extends AbstractExporter {
		/**
		 * LocationExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Location::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Location))
				throw new \InvalidArgumentException('$object must be an instance of ' . Location::class);

			$output = LocationEntityData::fromEntity($object)->normalize();

			return new Export('locations', $output);
		}
	}