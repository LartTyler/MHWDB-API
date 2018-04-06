<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class WeaponAssets implements EntityInterface {
		use EntityTrait;

		/**
		 * @var Asset|null
		 */
		private $icon;

		/**
		 * @var Asset|null
		 */
		private $image;

		/**
		 * WeaponAssets constructor.
		 *
		 * @param Asset|null $icon
		 * @param Asset|null $image
		 */
		public function __construct(?Asset $icon, ?Asset $image) {
			$this->icon = $icon;
			$this->image = $image;
		}

		/**
		 * @return Asset|null
		 */
		public function getIcon(): ?Asset {
			return $this->icon;
		}

		/**
		 * @param Asset|null $icon
		 *
		 * @return $this
		 */
		public function setIcon(?Asset $icon) {
			$this->icon = $icon;

			return $this;
		}

		/**
		 * @return Asset|null
		 */
		public function getImage(): ?Asset {
			return $this->image;
		}

		/**
		 * @param Asset|null $image
		 *
		 * @return $this
		 */
		public function setImage(?Asset $image) {
			$this->image = $image;

			return $this;
		}
	}