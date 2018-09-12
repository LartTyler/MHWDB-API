<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\WeaponEntityData;
	use App\Entity\Weapon;
	use App\Export\Export;

	class WeaponExporter extends AbstractExporter {
		use AssetExporterTrait;

		/**
		 * WeaponExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Weapon::class);
		}

		/**
		 * @param Weapon|object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Weapon))
				throw new \InvalidArgumentException('$object must be an instance of ' . Weapon::class);

			$data = WeaponEntityData::fromEntity($object);
			$export = new Export('weapons/' . $object->getType(), $data->normalize());

			$assetExports = [];

			if ($assets = $data->getAssets()) {
				if ($asset = $assets->getIcon())
					$assetExports[] = $this->createAssetExport($asset);

				if ($asset = $assets->getImage())
					$assetExports[] = $this->createAssetExport($asset);
			}

			$export->setAssets($assetExports);

			return $export;
		}
	}