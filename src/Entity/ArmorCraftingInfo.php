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
	 * @ORM\Table(name="armor_crafting_info")
	 *
	 * Class ArmorCraftingInfo
	 *
	 * @package App\Entity
	 */
	class ArmorCraftingInfo implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\ManyToMany(targetEntity="App\Entity\CraftingMaterialCost", orphanRemoval=true, cascade={"all"})
		 * @ORM\JoinTable(
		 *     name="armor_crafting_material_costs",
		 *     inverseJoinColumns={
		 *         @ORM\JoinColumn(unique=true)
		 *     }
		 * )
		 *
		 * @var Collection|Selectable|CraftingMaterialCost[]
		 */
		private $materials;

		/**
		 * @ORM\Column(type="integer", nullable=false, options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "materials.length"
		 */
		private $materialsLength = 0;

		/**
		 * ArmorCraftingInfo constructor.
		 */
		public function __construct() {
			$this->materials = new ArrayCollection();
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