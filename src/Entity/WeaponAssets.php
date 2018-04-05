<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class WeaponAssets implements EntityInterface {
		use EntityTrait;

		/**
		 * @var Asset
		 */
		private $icon;

		/**
		 * @var Asset
		 */
		private $image;

		/**
		 * WeaponAssets constructor.
		 *
		 * @param Asset $icon
		 * @param Asset $image
		 */
		public function __construct(Asset $icon, Asset $image) {
			$this->icon = $icon;
			$this->image = $image;
		}

		/**
		 * @return Asset
		 */
		public function getIcon(): Asset {
			return $this->icon;
		}

		/**
		 * @param Asset $icon
		 *
		 * @return $this
		 */
		public function setIcon(Asset $icon) {
			$this->icon = $icon;

			return $this;
		}

		/**
		 * @return Asset
		 */
		public function getImage(): Asset {
			return $this->image;
		}

		/**
		 * @param Asset $image
		 *
		 * @return $this
		 */
		public function setImage(Asset $image) {
			$this->image = $image;

			return $this;
		}
	}