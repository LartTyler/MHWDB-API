<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\ArmorSetBonusImporter;

	class ArmorSetBonusDataManager extends AbstractDataManager {
		/**
		 * ArmorSetBonusDataManager constructor.
		 *
		 * @param ContribManager        $contribManager
		 * @param ArmorSetBonusImporter $importer
		 */
		public function __construct(ContribManager $contribManager, ArmorSetBonusImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::ARMOR_SET_BONUSES), $importer);
		}
	}