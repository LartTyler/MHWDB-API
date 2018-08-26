<?php
	namespace App\Entity;

	use App\Game\Element;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Embeddable()
	 *
	 * Class Resistances
	 *
	 * @package App\Entity
	 */
	class Resistances implements \JsonSerializable {
		/**
		 * @ORM\Column(type="integer")
		 *
		 * @var int
		 */
		protected $fire = 0;

		/**
		 * @ORM\Column(type="integer")
		 *
		 * @var int
		 */
		protected $water = 0;

		/**
		 * @ORM\Column(type="integer")
		 *
		 * @var int
		 */
		protected $ice = 0;

		/**
		 * @ORM\Column(type="integer")
		 *
		 * @var int
		 */
		protected $thunder = 0;

		/**
		 * @ORM\Column(type="integer")
		 *
		 * @var int
		 */
		protected $dragon = 0;

		/**
		 *
		 * @return int
		 */
		public function getFire(): int {
			return $this->fire;
		}

		/**
		 * @param int $fire
		 *
		 * @return $this
		 */
		public function setFire(int $fire) {
			$this->fire = $fire;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getWater(): int {
			return $this->water;
		}

		/**
		 * @param int $water
		 *
		 * @return $this
		 */
		public function setWater(int $water) {
			$this->water = $water;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getIce(): int {
			return $this->ice;
		}

		/**
		 * @param int $ice
		 *
		 * @return $this
		 */
		public function setIce(int $ice) {
			$this->ice = $ice;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getThunder(): int {
			return $this->thunder;
		}

		/**
		 * @param int $thunder
		 *
		 * @return $this
		 */
		public function setThunder(int $thunder) {
			$this->thunder = $thunder;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getDragon(): int {
			return $this->dragon;
		}

		/**
		 * @param int $dragon
		 *
		 * @return $this
		 */
		public function setDragon(int $dragon) {
			$this->dragon = $dragon;

			return $this;
		}

		/**
		 * @return array
		 */
		public function jsonSerialize(): array {
			return [
				Element::FIRE => $this->getFire(),
				Element::WATER => $this->getWater(),
				Element::ICE => $this->getIce(),
				Element::THUNDER => $this->getThunder(),
				Element::DRAGON => $this->getDragon(),
			];
		}
	}