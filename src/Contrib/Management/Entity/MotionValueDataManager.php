<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\MotionValueImporter;

	class MotionValueDataManager extends AbstractDataManager {
		/**
		 * MotionValueDataManager constructor.
		 *
		 * @param ContribManager      $contribManager
		 * @param MotionValueImporter $importer
		 */
		public function __construct(ContribManager $contribManager, MotionValueImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::MOTION_VALUES), $importer);
		}
	}