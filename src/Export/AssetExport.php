<?php
	namespace App\Export;

	use App\Entity\Asset;

	class AssetExport {
		/**
		 * @var string
		 */
		protected $uri;

		/**
		 * @var string
		 */
		protected $primaryHash;

		/**
		 * @var string
		 */
		protected $secondaryHash;

		/**
		 * AssetExport constructor.
		 *
		 * @param string $uri
		 * @param string $primaryHash
		 * @param string $secondaryHash
		 */
		public function __construct(string $uri, string $primaryHash, string $secondaryHash) {
			$this->uri = $uri;
			$this->primaryHash = $primaryHash;
			$this->secondaryHash = $secondaryHash;
		}

		/**
		 * @return string
		 */
		public function getUri(): string {
			return $this->uri;
		}

		/**
		 * @return string
		 */
		public function getPrimaryHash(): string {
			return $this->primaryHash;
		}

		/**
		 * @return string
		 */
		public function getSecondaryHash(): string {
			return $this->secondaryHash;
		}

		/**
		 * @param Asset  $asset
		 *
		 * @return static
		 */
		public static function fromAsset(Asset $asset) {
			return new static($asset->getUri(), $asset->getPrimaryHash(), $asset->getSecondaryHash());
		}
	}