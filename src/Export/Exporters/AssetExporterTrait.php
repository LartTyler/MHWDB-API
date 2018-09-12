<?php
	namespace App\Export\Exporters;

	use App\Contrib\Data\AssetEntityData;
	use App\Export\AssetExport;

	trait AssetExporterTrait {
		/**
		 * @param AssetEntityData $asset
		 *
		 * @return AssetExport
		 */
		protected function createAssetExport(AssetEntityData $asset): AssetExport {
			return new AssetExport($asset->getUri(), $asset->getPrimaryHash(), $asset->getSecondaryHash());
		}
	}