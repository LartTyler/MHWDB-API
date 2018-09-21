<?php
	namespace App\Contrib\Data;

	use App\Entity\ArmorAssets;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class ArmorAssetsEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see ArmorAssets
	 */
	class ArmorAssetsEntityData extends AbstractEntityData {
		/**
		 * @var AssetEntityData|null
		 */
		protected $imageMale = null;

		/**
		 * @var AssetEntityData|null
		 */
		protected $imageFemale = null;

		/**
		 * @return AssetEntityData|null
		 */
		public function getImageMale(): ?AssetEntityData {
			return $this->imageMale;
		}

		/**
		 * @param AssetEntityData|null $imageMale
		 *
		 * @return $this
		 */
		public function setImageMale(?AssetEntityData $imageMale) {
			$this->imageMale = $imageMale;

			return $this;
		}

		/**
		 * @return AssetEntityData|null
		 */
		public function getImageFemale(): ?AssetEntityData {
			return $this->imageFemale;
		}

		/**
		 * @param AssetEntityData|null $imageFemale
		 *
		 * @return $this
		 */
		public function setImageFemale(?AssetEntityData $imageFemale) {
			$this->imageFemale = $imageFemale;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'imageMale' => $this->getImageMale() ? $this->getImageMale()->normalize() : null,
				'imageFemale' => $this->getImageFemale() ? $this->getImageFemale()->normalize() : null,
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'imageMale')) {
				$value = $data->imageMale;

				if ($value && $this->getImageMale())
					$this->getImageMale()->update($value);
				else if ($value)
					$this->setImageMale(AssetEntityData::fromJson($value));
				else
					$this->setImageMale(null);
			}

			if (ObjectUtil::isset($data, 'imageFemale')) {
				$value = $data->imageFemale;

				if ($value && $this->getImageFemale())
					$this->getImageFemale()->update($value);
				else if ($value)
					$this->setImageFemale(AssetEntityData::fromJson($value));
				else
					$this->setImageFemale(null);
			}
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static();

			if ($value = $source->imageMale)
				$data->imageMale = AssetEntityData::fromJson($value);

			if ($value = $source->imageFemale)
				$data->imageFemale = AssetEntityData::fromJson($value);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof ArmorAssets))
				throw static::createLoadFailedException(ArmorAssets::class);

			$data = new static();

			if ($asset = $entity->getImageMale())
				$data->imageMale = AssetEntityData::fromEntity($asset);

			if ($asset = $entity->getImageFemale())
				$data->imageFemale = AssetEntityData::fromEntity($asset);

			return $data;
		}
	}