<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class Asset implements EntityInterface {
		use EntityTrait;

		/**
		 * @var string
		 */
		private $uri;

		/**
		 * @var string
		 */
		private $primaryHash;

		/**
		 * @var string
		 */
		private $secondaryHash;

		/**
		 * Asset constructor.
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
	}