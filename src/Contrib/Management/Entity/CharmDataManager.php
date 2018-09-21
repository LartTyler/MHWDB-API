<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\CharmImporter;

	class CharmDataManager extends AbstractDataManager {
		/**
		 * CharmDataManager constructor.
		 *
		 * @param ContribManager $contribManager
		 * @param CharmImporter  $importer
		 */
		public function __construct(ContribManager $contribManager, CharmImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::CHARMS), $importer);
		}
	}