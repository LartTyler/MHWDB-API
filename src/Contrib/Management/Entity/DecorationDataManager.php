<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\DecorationImporter;

	class DecorationDataManager extends AbstractDataManager {
		/**
		 * DecorationDataManager constructor.
		 *
		 * @param ContribManager     $contribManager
		 * @param DecorationImporter $importer
		 */
		public function __construct(ContribManager $contribManager, DecorationImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::DECORATIONS), $importer);
		}
	}