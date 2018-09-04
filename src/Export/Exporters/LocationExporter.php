<?php
	namespace App\Export\Exporters;

	use App\Entity\Camp;
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

			$output = [
				'name' => $object->getName(),
				'zoneCount' => $object->getZoneCount(),
				'camps' => $object->getCamps()->map(function(Camp $camp): array {
					return [
						'name' => $camp->getName(),
						'zone' => $camp->getZone(),
					];
				})->toArray(),
			];

			ksort($output);

			return new Export('locations', $output);
		}
	}