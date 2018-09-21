<?php
	namespace App\Contrib\Data;

	use App\Entity\Asset;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class AssetEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Asset
	 */
	class AssetEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $primaryHash;

		/**
		 * @var string
		 */
		protected $secondaryHash;

		/**
		 * @var string
		 */
		protected $uri;

		/**
		 * AssetEntityData constructor.
		 *
		 * @param string $primaryHash
		 * @param string $secondaryHash
		 * @param string $uri
		 */
		public function __construct(string $primaryHash, string $secondaryHash, string $uri) {
			$this->primaryHash = $primaryHash;
			$this->secondaryHash = $secondaryHash;
			$this->uri = parse_url($uri, PHP_URL_PATH);
		}

		/**
		 * @return string
		 */
		public function getPrimaryHash(): string {
			return $this->primaryHash;
		}

		/**
		 * @param string $primaryHash
		 *
		 * @return $this
		 */
		public function setPrimaryHash(string $primaryHash) {
			$this->primaryHash = $primaryHash;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getSecondaryHash(): string {
			return $this->secondaryHash;
		}

		/**
		 * @param string $secondaryHash
		 *
		 * @return $this
		 */
		public function setSecondaryHash(string $secondaryHash) {
			$this->secondaryHash = $secondaryHash;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getUri(): string {
			return $this->uri;
		}

		/**
		 * @param string $uri
		 *
		 * @return $this
		 */
		public function setUri(string $uri) {
			$this->uri = parse_url($uri, PHP_URL_PATH);

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'primaryHash' => $this->getPrimaryHash(),
				'secondaryHash' => $this->getSecondaryHash(),
				'uri' => $this->getUri(),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'primaryHash'))
				$this->setPrimaryHash($data->primaryHash);

			if (ObjectUtil::isset($data, 'secondaryHash'))
				$this->setSecondaryHash($data->secondaryHash);

			if (ObjectUtil::isset($data, 'uri'))
				$this->setUri($data->uri);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			return new static($source->primaryHash, $source->secondaryHash, $source->uri);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof Asset))
				throw static::createLoadFailedException(Asset::class);

			return new static($entity->getPrimaryHash(), $entity->getSecondaryHash(), $entity->getUri());
		}
	}