<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class MotionValue implements EntityInterface {
		use EntityTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var string
		 */
		private $weaponType;

		/**
		 * @var string
		 */
		private $damageType = null;

		/**
		 * @var int|null
		 */
		private $stun = null;

		/**
		 * @var int|null
		 */
		private $exhaust = null;

		/**
		 * @var int[]
		 */
		private $hits = [];

		/**
		 * MotionValue constructor.
		 *
		 * @param string $name
		 * @param string $weaponType
		 */
		public function __construct(string $name, string $weaponType) {
			$this->name = $name;
			$this->weaponType = $weaponType;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name) {
			$this->name = $name;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getWeaponType(): string {
			return $this->weaponType;
		}

		/**
		 * @param string|null $weaponType
		 *
		 * @return $this
		 */
		public function setWeaponType(?string $weaponType) {
			$this->weaponType = $weaponType;

			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getDamageType(): ?string {
			return $this->damageType;
		}

		/**
		 * @param string|null $damageType
		 *
		 * @return $this
		 */
		public function setDamageType(?string $damageType) {
			$this->damageType = $damageType;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getStun(): ?int {
			return $this->stun;
		}

		/**
		 * @param int|null $stun
		 *
		 * @return $this
		 */
		public function setStun(?int $stun) {
			$this->stun = $stun;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getExhaust(): ?int {
			return $this->exhaust;
		}

		/**
		 * @param int|null $exhaust
		 *
		 * @return $this
		 */
		public function setExhaust(?int $exhaust) {
			$this->exhaust = $exhaust;

			return $this;
		}

		/**
		 * @return int[]
		 */
		public function getHits(): array {
			return $this->hits;
		}

		/**
		 * @param int[] $hits
		 *
		 * @return $this
		 */
		public function setHits(array $hits) {
			$this->hits = $hits;

			return $this;
		}
	}