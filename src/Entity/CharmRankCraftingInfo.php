<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;

	class CharmRankCraftingInfo implements EntityInterface {
		use EntityTrait;

		/**
		 * @var bool
		 */
		private $craftable;

		/**
		 * @var Collection|Selectable|CraftingMaterialCost[]
		 */
		private $materials;

		/**
		 * CharmCraftingInfo constructor.
		 *
		 * @param bool $craftable
		 */
		public function __construct(bool $craftable = false) {
			$this->craftable = $craftable;
			$this->materials = new ArrayCollection();
		}

		/**
		 * @return bool
		 */
		public function isCraftable(): bool {
			return $this->craftable;
		}

		/**
		 * @param bool $craftable
		 *
		 * @return $this
		 */
		public function setCraftable(bool $craftable) {
			$this->craftable = $craftable;

			return $this;
		}

		/**
		 * @return CraftingMaterialCost[]|Collection|Selectable
		 */
		public function getMaterials() {
			return $this->materials;
		}
	}