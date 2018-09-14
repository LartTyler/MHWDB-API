<?php
	namespace App\Import\Importers;

	use App\Import\AssetManager;

	trait AssetManagerAwareTrait {
		/**
		 * @var AssetManager|null
		 */
		protected $assetManager = null;

		/**
		 * @required
		 *
		 * @param AssetManager $assetManager
		 *
		 * @return void
		 */
		public function setAssetManager(AssetManager $assetManager) {
			$this->assetManager = $assetManager;
		}
	}