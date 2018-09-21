<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\LocationImporter;

	class LocationDataManager extends AbstractDataManager {
		/**
		 * LocationDataManager constructor.
		 *
		 * @param ContribManager   $contribManager
		 * @param LocationImporter $importer
		 */
		public function __construct(ContribManager $contribManager, LocationImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::LOCATIONS), $importer);
		}
	}