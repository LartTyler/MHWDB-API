<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Embeddable()
	 *
	 * Class ArmorDefenseValues
	 *
	 * @package App\Entity
	 */
	class ArmorDefenseValues implements \JsonSerializable {
		/**
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $base = 0;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $max = 0;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $augmented = 0;

		/**
		 * @return int
		 */
		public function getBase(): int {
			return $this->base;
		}

		/**
		 * @param int $base
		 *
		 * @return $this
		 */
		public function setBase(int $base) {
			$this->base = $base;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getMax(): int {
			return $this->max;
		}

		/**
		 * @param int $max
		 *
		 * @return $this
		 */
		public function setMax(int $max) {
			$this->max = $max;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getAugmented(): int {
			return $this->augmented;
		}

		/**
		 * @param int $augmented
		 *
		 * @return $this
		 */
		public function setAugmented(int $augmented) {
			$this->augmented = $augmented;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function jsonSerialize(): array {
			return [
				'base' => $this->getBase(),
				'max' => $this->getMax(),
				'augmented' => $this->getAugmented(),
			];
		}
	}