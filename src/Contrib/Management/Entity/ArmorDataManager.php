<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Armor;
	use App\Import\AssetManager;
	use App\Import\Importers\ArmorImporter;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ArmorDataManager extends AbstractDataManager {
		use AssetAwareManagerTrait;

		/**
		 * @var AssetManager
		 */
		protected $assetManager;

		/**
		 * ArmorDataManager constructor.
		 *
		 * @param ContribManager $contribManager
		 * @param ArmorImporter  $importer
		 */
		public function __construct(ContribManager $contribManager, ArmorImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::ARMOR), $importer);
		}

		/**
		 * @param Armor|EntityInterface $entity
		 *
		 * @return void
		 */
		public function export(EntityInterface $entity): void {
			parent::export($entity);

			if ($assets = $entity->getAssets()) {
				$this->exportAsset($this->contribGroup, $assets->getImageMale());

				$this->exportAsset($this->contribGroup, $assets->getImageFemale());
			}
		}
	}