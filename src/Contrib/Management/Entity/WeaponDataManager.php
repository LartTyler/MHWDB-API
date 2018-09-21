<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Weapon;
	use App\Import\Importers\WeaponImporter;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class WeaponDataManager extends AbstractDataManager {
		use AssetAwareManagerTrait;

		/**
		 * WeaponDataManager constructor.
		 *
		 * @param ContribManager $contribManager
		 * @param WeaponImporter $importer
		 */
		public function __construct(ContribManager $contribManager, WeaponImporter $importer) {
			parent::__construct($contribManager->getGroup(EntityType::WEAPONS), $importer);
		}

		/**
		 * @param Weapon|EntityInterface $entity
		 *
		 * @return void
		 */
		public function export(EntityInterface $entity): void {
			parent::export($entity);

			if ($assets = $entity->getAssets()) {
				$this->exportAsset($this->contribGroup, $assets->getIcon());

				$this->exportAsset($this->contribGroup, $assets->getImage());
			}
		}
	}