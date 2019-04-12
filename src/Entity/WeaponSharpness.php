<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="weapon_sharpnesses")
	 *
	 * Class WeaponSharpness
	 *
	 * @package App\Entity
	 */
	class WeaponSharpness implements EntityInterface {
		use EntityTrait;

		/**
		 * @Assert\Range(min=0)
		 *
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $red = 0;

		/**
		 * @Assert\Range(min=0)
		 *
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $orange = 0;

		/**
		 * @Assert\Range(min=0)
		 *
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $yellow = 0;

		/**
		 * @Assert\Range(min=0)
		 *
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $green = 0;

		/**
		 * @Assert\Range(min=0)
		 *
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $blue = 0;

		/**
		 * @Assert\Range(min=0)
		 *
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $white = 0;

		/**
		 * @return int
		 */
		public function getRed(): int {
			return $this->red;
		}

		/**
		 * @param int $red
		 *
		 * @return $this
		 */
		public function setRed(int $red) {
			$this->red = $red;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getOrange(): int {
			return $this->orange;
		}

		/**
		 * @param int $orange
		 *
		 * @return $this
		 */
		public function setOrange(int $orange) {
			$this->orange = $orange;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getYellow(): int {
			return $this->yellow;
		}

		/**
		 * @param int $yellow
		 *
		 * @return $this
		 */
		public function setYellow(int $yellow) {
			$this->yellow = $yellow;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getGreen(): int {
			return $this->green;
		}

		/**
		 * @param int $green
		 *
		 * @return $this
		 */
		public function setGreen(int $green) {
			$this->green = $green;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getBlue(): int {
			return $this->blue;
		}

		/**
		 * @param int $blue
		 *
		 * @return $this
		 */
		public function setBlue(int $blue) {
			$this->blue = $blue;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getWhite(): int {
			return $this->white;
		}

		/**
		 * @param int $white
		 *
		 * @return $this
		 */
		public function setWhite(int $white) {
			$this->white = $white;

			return $this;
		}

		/**
		 * @param WeaponSharpness $other
		 *
		 * @return $this
		 */
		public function import(WeaponSharpness $other) {
			$this
				->setRed($other->getRed())
				->setOrange($other->getOrange())
				->setYellow($other->getYellow())
				->setGreen($other->getGreen())
				->setBlue($other->getBlue())
				->setWhite($other->getWhite());

			return $this;
		}
	}