<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\AilmentImporter;

	class AilmentDataManager extends AbstractDataManager {
		/**
		 * AilmentDataManager constructor.
		 *
		 * @param ContribManager $contribManager
		 * @param AilmentImporter $importer
		 */
		public function __construct(ContribManager $contribManager, AilmentImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::AILMENTS), $importer);
		}
	}