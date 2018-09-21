<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\Importers\SkillImporter;

	class SkillDataManager extends AbstractDataManager {
		/**
		 * SkillDataManager constructor.
		 *
		 * @param ContribManager $contribManager
		 * @param SkillImporter  $importer
		 */
		public function __construct(ContribManager $contribManager, SkillImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::SKILLS), $importer);
		}
	}