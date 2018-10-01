<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Embeddable()
	 *
	 * Class WeaponAttackValues
	 *
	 * @package App\Entity
	 */
	class WeaponAttackValues implements EntityInterface {
		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $display = 0;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $raw = 0;

		/**
		 * @return null
		 */
		public function getId() {
			return null;
		}

		/**
		 * @return int
		 */
		public function getDisplay(): int {
			return $this->display;
		}

		/**
		 * @param int $display
		 *
		 * @return $this
		 */
		public function setDisplay(int $display) {
			$this->display = $display;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getRaw(): int {
			return $this->raw;
		}

		/**
		 * @param int $raw
		 *
		 * @return $this
		 */
		public function setRaw(int $raw) {
			$this->raw = $raw;

			return $this;
		}

		/**
		 * @param WeaponAttackValues $other
		 *
		 * @return $this
		 */
		public function import(WeaponAttackValues $other) {
			$this
				->setDisplay($other->getDisplay())
				->setRaw($other->getRaw());

			return $this;
		}
	}