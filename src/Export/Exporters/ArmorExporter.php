<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\ArmorEntityData;
	use App\Entity\Armor;
	use App\Export\Export;

	class ArmorExporter extends AbstractExporter {
		use AssetExporterTrait;

		/**
		 * ArmorExporter constructor.
		 */
		public function __construct() {
			parent::__construct(Armor::class);
		}

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export {
			if (!($object instanceof Armor))
				throw new \InvalidArgumentException('$object must be an instance of ' . Armor::class);

			$data = ArmorEntityData::fromEntity($object);
			$export = new Export('armor/' . $object->getRank(), $data->normalize());

			$assetExports = [];

			if ($assets = $data->getAssets()) {
				if ($image = $assets->getImageFemale())
					$assetExports[] = $this->createAssetExport($image);

				if ($image = $assets->getImageMale())
					$assetExports[] = $this->createAssetExport($image);
			}

			$export->setAssets($assetExports);

			return $export;
		}
	}