<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class ArmorAssets implements EntityInterface {
		use EntityTrait;

		/**
		 * @var Asset
		 */
		private $imageMale;

		/**
		 * @var Asset
		 */
		private $imageFemale;

		/**
		 * ArmorAssets constructor.
		 *
		 * @param Asset $imageMale
		 * @param Asset $imageFemale
		 */
		public function __construct(Asset $imageMale, Asset $imageFemale) {
			$this->imageMale = $imageMale;
			$this->imageFemale = $imageFemale;
		}

		/**
		 * @return Asset
		 */
		public function getImageMale(): Asset {
			return $this->imageMale;
		}

		/**
		 * @param Asset $imageMale
		 *
		 * @return $this
		 */
		public function setImageMale(Asset $imageMale) {
			$this->imageMale = $imageMale;

			return $this;
		}

		/**
		 * @return Asset
		 */
		public function getImageFemale(): Asset {
			return $this->imageFemale;
		}

		/**
		 * @param Asset $imageFemale
		 *
		 * @return $this
		 */
		public function setImageFemale(Asset $imageFemale) {
			$this->imageFemale = $imageFemale;

			return $this;
		}
	}