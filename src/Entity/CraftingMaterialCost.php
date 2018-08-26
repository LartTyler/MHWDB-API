<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="crafting_material_costs")
	 *
	 * Class CraftingMaterialCost
	 *
	 * @package App\Entity
	 */
	class CraftingMaterialCost implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Item")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Item
		 */
		private $item;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
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