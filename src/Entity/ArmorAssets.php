<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="armor_assets")
	 *
	 * Class ArmorAssets
	 *
	 * @package App\Entity
	 */
	class ArmorAssets implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Asset", cascade={"persist"})
		 *
		 * @var Asset|null
		 */
		private $imageMale;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Asset", cascade={"persist"})
		 *
		 * @var Asset|null
		 */
		private $imageFemale;

		/**
		 * ArmorAssets constructor.
		 *
		 * @param Asset|null $imageMale
		 * @param Asset|null $imageFemale
		 */
		public function __construct(?Asset $imageMale, ?Asset $imageFemale) {
			$this->imageMale = $imageMale;
			$this->imageFemale = $imageFemale;
		}

		/**
		 * @return Asset|null
		 */
		public function getImageMale(): ?Asset {
			return $this->imageMale;
		}

		/**
		 * @param Asset|null $imageMale
		 *
		 * @return $this
		 */
		public function setImageMale(?Asset $imageMale) {
			$this->imageMale = $imageMale;

			return $this;
		}

		/**
		 * @return Asset|null
		 */
		public function getImageFemale(): ?Asset {
			return $this->imageFemale;
		}

		/**
		 * @param Asset|null $imageFemale
		 *
		 * @return $this
		 */
		public function setImageFemale(?Asset $imageFemale) {
			$this->imageFemale = $imageFemale;

			return $this;
		}
	}