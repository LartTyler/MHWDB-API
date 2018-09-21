<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\ArmorSetImporter;

	class ArmorSetDataManager extends AbstractDataManager {
		/**
		 * ArmorSetDataManager constructor.
		 *
		 * @param ContribManager   $contribManager
		 * @param ArmorSetImporter $importer
		 */
		public function __construct(ContribManager $contribManager, ArmorSetImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::ARMOR_SETS), $importer);
		}
	}