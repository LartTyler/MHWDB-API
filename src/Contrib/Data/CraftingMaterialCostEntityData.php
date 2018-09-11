<?php
	namespace App\Contrib\Data;

	use App\Entity\CraftingMaterialCost;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class CraftingMaterialCostEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see CraftingMaterialCost
	 */
	class CraftingMaterialCostEntityData extends AbstractEntityData {
		/**
		 * @var int
		 */
		protected $item;

		/**
		 * @var int
		 */
		protected $quantity;

		/**
		 * CraftingMaterialCostEntityData constructor.
		 *
		 * @param int $item
		 * @param int $quantity
		 */
		public function __construct(int $item, int $quantity) {
			$this->item = $item;
			$this->quantity = $quantity;
		}

		/**
		 * @return int
		 */
		public function getItem(): int {
			return $this->item;
		}

		/**
		 * @param int $item
		 *
		 * @return $this
		 */
		public function setItem(int $item) {
			$this->item = $item;

			return $this;
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

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'item' => $this->getItem(),
				'quantity' => $this->getQuantity(),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'item'))
				$this->setItem($data->item);

			if (ObjectUtil::isset($data, 'quantity'))
				$this->setQuantity($data->quantity);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			return new static($source->item, $source->quantity);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof CraftingMaterialCost))
				throw static::createLoadFailedException(CraftingMaterialCost::class);

			return new static($entity->getItem()->getId(), $entity->getQuantity());
		}
	}