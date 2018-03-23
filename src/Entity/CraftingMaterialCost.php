<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class CraftingMaterialCost implements EntityInterface {
		use EntityTrait;

		/**
		 * @var Item
		 */
		private $item;

		/**
		 * @var int
		 */
		private $quantity;

		/**
		 * CraftingMaterialCost constructor.
		 *
		 * @param Item $item
		 * @param int  $quantity
		 */
		public function __construct(Item $item, int $quantity) {
			$this->item = $item;
			$this->quantity = $quantity;
		}

		/**
		 * @return Item
		 */
		public function getItem(): Item {
			return $this->item;
		}

		/**
		 * @return int
		 */
		public function getQuantity(): int {
			return $this->quantity;
		}

		/**
		 * @param int $quantity
		 *
		 * @return $this
		 */
		public function setQuantity(int $quantity) {
			$this->quantity = $quantity;
			return $this;
		}
	}