<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="charm_rank_crafting_info")
	 *
	 * Class CharmRankCraftingInfo
	 *
	 * @package App\Entity
	 */
	class CharmRankCraftingInfo implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="boolean")
		 *
		 * @var bool
		 */
		private $craftable;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\CraftingMaterialCost", orphanRemoval=true, cascade={"all"})
		 * @ORM\JoinTable(name="charm_rank_crafting_info_crafting_material_costs")
		 *
		 * @var Collection|Selectable|CraftingMaterialCost[]
		 */
		private $materials;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "materials.length"
		 */
		private $materialsLength = 0;

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

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->materialsLength = $this->materials->count();
		}
	}