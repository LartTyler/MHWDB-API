<?php
	namespace App\Contrib\Data;

	use App\Entity\WeaponAssets;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class WeaponAssetsEntityData
	 *
	 * @package App\Contrib\Data
	 * @see     WeaponAssets
	 */
	class WeaponAssetsEntityData extends AbstractEntityData {
		/**
		 * @var AssetEntityData|null
		 */
		protected $icon = null;

		/**
		 * @var AssetEntityData|null
		 */
		protected $image = null;

		/**
		 * @return AssetEntityData|null
		 */
		public function getIcon(): ?AssetEntityData {
			return $this->icon;
		}

		/**
		 * @param AssetEntityData|null $icon
		 *
		 * @return $this
		 */
		public function setIcon(?AssetEntityData $icon) {
			$this->icon = $icon;

			return $this;
		}

		/**
		 * @return AssetEntityData|null
		 */
		public function getImage(): ?AssetEntityData {
			return $this->image;
		}

		/**
		 * @param AssetEntityData|null $image
		 *
		 * @return $this
		 */
		public function setImage(?AssetEntityData $image) {
			$this->image = $image;

			return $this;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'icon'))
				$this->setIcon(AssetEntityData::fromJson($data->icon));

			if (ObjectUtil::isset($data, 'image'))
				$this->setImage(AssetEntityData::fromJson($data->image));
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'icon' => $this->getIcon() ? $this->getIcon()->normalize() : null,
				'image' => $this->getImage() ? $this->getImage()->normalize() : null,
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static();
			$data->icon = $source->icon;
			$data->image = $source->image;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof WeaponAssets))
				throw static::createLoadFailedException(WeaponAssets::class);

			$data = new static();

			if ($icon = $entity->getIcon())
				$data->icon = AssetEntityData::fromEntity($icon);

			if ($image = $entity->getImage())
				$data->image = AssetEntityData::fromEntity($image);

			return $data;
		}
	}