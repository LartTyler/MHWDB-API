<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\ItemImporter;

	class ItemDataManager extends AbstractDataManager {
		/**
		 * ItemDataManager constructor.
		 *
		 * @param ContribManager $contribManager
		 * @param ItemImporter   $importer
		 */
		public function __construct(ContribManager $contribManager, ItemImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::ITEMS), $importer);
		}
	}