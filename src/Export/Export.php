<?php
	namespace App\Export;

	class Export {
		/**
		 * @var array
		 */
		protected $data;

		/**
		 * @var string
		 */
		protected $group;

		/**
		 * @var AssetExport[]
		 */
		protected $assets = [];

		/**
		 * Export constructor.
		 *
		 * @param string $group
		 * @param array  $data
		 */
		public function __construct(string $group, array $data) {
			$this->group = $group;
			$this->data = $data;
		}

		/**
		 * @return array
		 */
		public function getData(): array {
			return $this->data;
		}

		/**
		 * @return string
		 */
		public function getGroup(): string {
			return $this->group;
		}

		/**
		 * @return AssetExport[]
		 */
		public function getAssets(): array {
			return $this->assets;
		}

		/**
		 * @param AssetExport[] $assets
		 *
		 * @return $this
		 */
		public function setAssets(array $assets) {
			$this->assets = $assets;

			return $this;
		}
	}