<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class Item implements EntityInterface {
		use EntityTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var string
		 */
		private $description;

		/**
		 * @var int
		 */
		private $rarity;

		/**
		 * @var int
		 */
		private $sellPrice = 0;

		/**
		 * @var int
		 */
		private $buyPrice = 0;

		/**
		 * @var int
		 */
		private $carryLimit = 0;

		/**
		 * Item constructor.
		 *
		 * @param string $name
		 * @param string $description
		 * @param int    $rarity
		 */
		public function __construct(string $name, string $description, int $rarity) {
			$this->name = $name;
			$this->description = $description;
			$this->rarity = $rarity;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return string
		 */
		public function getDescription(): string {
			return $this->description;
		}

		/**
		 * @param string $description
		 *
		 * @return $this
		 */
		public function setDescription(string $description) {
			$this->description = $description;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getRarity(): int {
			return $this->rarity;
		}

		/**
		 * @param int $rarity
		 *
		 * @return $this
		 */
		public function setRarity(int $rarity) {
			$this->rarity = $rarity;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getSellPrice(): int {
			return $this->sellPrice;
		}

		/**
		 * @param int $sellPrice
		 *
		 * @return $this
		 */
		public function setSellPrice(int $sellPrice) {
			$this->sellPrice = $sellPrice;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getBuyPrice(): int {
			return $this->buyPrice;
		}

		/**
		 * @param int $buyPrice
		 *
		 * @return $this
		 */
		public function setBuyPrice(int $buyPrice) {
			$this->buyPrice = $buyPrice;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getCarryLimit(): int {
			return $this->carryLimit;
		}

		/**
		 * @param int $carryLimit
		 *
		 * @return $this
		 */
		public function setCarryLimit(int $carryLimit) {
			$this->carryLimit = $carryLimit;
			return $this;
		}
	}