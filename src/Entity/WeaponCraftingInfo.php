<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="weapon_crafting_info")
	 *
	 * Class WeaponCraftingInfo
	 *
	 * @package App\Entity
	 */
	class WeaponCraftingInfo implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="boolean")
		 *
		 * @var bool
		 */
		private $craftable;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Weapon")
		 *
		 * @var Weapon|null
		 */
		private $previous;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\Weapon")
		 * @ORM\JoinTable(name="weapon_crafting_info_branches")
		 *
		 * @var Collection|Selectable|Weapon[]
		 */
		private $branches;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\ManyToMany(targetEntity="App\Entity\CraftingMaterialCost", orphanRemoval=true, cascade={"all"})
		 * @ORM\JoinTable(name="weapon_crafting_info_crafting_material_costs")
		 *
		 * @var Collection|Selectable|CraftingMaterialCost[]
		 */
		private $craftingMaterials;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\ManyToMany(targetEntity="App\Entity\CraftingMaterialCost", orphanRemoval=true, cascade={"all"})
		 * @ORM\JoinTable(name="weapon_crafting_info_upgrade_material_costs")
		 *
		 * @var Collection|Selectable|CraftingMaterialCost[]
		 */
		private $upgradeMaterials;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "branches.length"
		 */
		private $branchesLength = 0;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "craftingMaterials.length"
		 */
		private $craftingMaterialsLength = 0;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "upgradeMaterials.length"
		 */
		private $upgradeMaterialsLength = 0;

		/**
		 * WeaponCraftingInfo constructor.
		 *
		 * @param bool        $craftable
		 * @param Weapon|null $previous
		 */
		public function __construct(bool $craftable, ?Weapon $previous = null) {
			$this->craftable = $craftable;
			$this->previous = $previous;

			$this->branches = new ArrayCollection();
			$this->craftingMaterials = new ArrayCollection();
			$this->upgradeMaterials = new ArrayCollection();
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
		 * @return Weapon|null
		 */
		public function getPrevious() {
			return $this->previous;
		}

		/**
		 * @param Weapon|null $previous
		 *
		 * @return $this
		 */
		public function setPrevious(?Weapon $previous) {
			$this->previous = $previous;

			return $this;
		}

		/**
		 * @return Weapon[]|Collection|Selectable
		 */
		public function getBranches() {
			return $this->branches;
		}

		/**
		 * @return CraftingMaterialCost[]|Collection|Selectable
		 */
		public function getCraftingMaterials() {
			return $this->craftingMaterials;
		}

		/**
		 * @return CraftingMaterialCost[]|Collection|Selectable
		 */
		public function getUpgradeMaterials() {
			return $this->upgradeMaterials;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->branchesLength = $this->branches->count();
			$this->craftingMaterialsLength = $this->craftingMaterials->count();
			$this->upgradeMaterialsLength = $this->upgradeMaterials->count();
		}
	}