<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="weapons",
	 *     indexes={
	 *         @ORM\Index(columns={"type"})
	 *     }
	 * )
	 *
	 * Class Weapon
	 *
	 * @package App\Entity
	 */
	class Weapon implements EntityInterface, SluggableInterface, LengthCachingEntityInterface {
		use EntityTrait;
		use SluggableTrait;
		use AttributableTrait;

		/**
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 */
		private $type;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $rarity;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\Slot", orphanRemoval=true, cascade={"all"})
		 * @ORM\JoinTable(name="weapon_slots")
		 *
		 * @var Collection|Selectable|Slot[]
		 */
		private $slots;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\WeaponSharpness", orphanRemoval=true, cascade={"all"})
		 * @ORM\JoinTable(name="weapon_durability")
		 *
		 * @var Collection|Selectable|WeaponSharpness[]
		 */
		private $durability;

		/**
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\WeaponElement",
		 *     mappedBy="weapon",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|WeaponElement[]
		 */
		private $elements;

		/**
		 * @ORM\Embedded(class="App\Entity\WeaponAttackValues", columnPrefix="attack_")
		 *
		 * @var WeaponAttackValues
		 */
		private $attack;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\WeaponCraftingInfo", orphanRemoval=true, cascade={"all"})
		 *
		 * @var WeaponCraftingInfo|null
		 */
		private $crafting = null;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\WeaponAssets", orphanRemoval=true, cascade={"all"})
		 *
		 * @var WeaponAssets|null
		 */
		private $assets = null;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "elements.length"
		 */
		private $elementsLength = 0;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "slots.length"
		 */
		private $slotsLength = 0;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "durability.length"
		 */
		private $durabilityLength = 0;

		/**
		 * Weapon constructor.
		 *
		 * @param string $name
		 * @param string $type
		 * @param int    $rarity
		 */
		public function __construct(string $name, string $type, int $rarity) {
			$this->name = $name;
			$this->type = $type;
			$this->rarity = $rarity;
			$this->slots = new ArrayCollection();
			$this->attack = new WeaponAttackValues();
			$this->elements = new ArrayCollection();
			$this->durability = new ArrayCollection();

			$this->setSlug($name);
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
		public function setName($name) {
			$this->name = $name;

			$this->setSlug($name);

			return $this;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
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
		public function setRarity(int $rarity): Weapon {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return Slot[]|Collection|Selectable
		 */
		public function getSlots() {
			return $this->slots;
		}

		/**
		 * @return WeaponCraftingInfo|null
		 */
		public function getCrafting(): ?WeaponCraftingInfo {
			return $this->crafting;
		}

		/**
		 * @param WeaponCraftingInfo|null $crafting
		 *
		 * @return $this
		 */
		public function setCrafting(WeaponCraftingInfo $crafting) {
			$this->crafting = $crafting;

			return $this;
		}

		/**
		 * @return WeaponAssets|null
		 */
		public function getAssets(): ?WeaponAssets {
			return $this->assets;
		}

		/**
		 * @param WeaponAssets|null $assets
		 *
		 * @return $this
		 */
		public function setAssets(?WeaponAssets $assets) {
			$this->assets = $assets;

			return $this;
		}

		/**
		 * @return WeaponElement[]|Collection|Selectable
		 */
		public function getElements() {
			return $this->elements;
		}

		/**
		 * @param string $element
		 *
		 * @return WeaponElement|null
		 */
		public function getElement(string $element): ?WeaponElement {
			$matches = $this->getElements()->matching(
				Criteria::create()->
					where(Criteria::expr()->eq('type', strtolower($element)))
			);

			if ($matches->count())
				return $matches->first();

			return null;
		}

		/**
		 * @param string $element
		 * @param int    $damage
		 * @param bool   $hidden
		 *
		 * @return $this
		 */
		public function setElement(string $element, int $damage, bool $hidden = false) {
			$element = strtolower($element);

			if ($object = $this->getElement($element)) {
				$object
					->setDamage($damage)
					->setHidden($hidden);
			} else
				$this->getElements()->add(new WeaponElement($this, $element, $damage, $hidden));

			return $this;
		}

		/**
		 * @return WeaponAttackValues
		 */
		public function getAttack(): WeaponAttackValues {
			return $this->attack;
		}

		/**
		 * @return WeaponSharpness[]|Collection|Selectable
		 */
		public function getDurability() {
			return $this->durability;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->elementsLength = $this->elements->count();
			$this->slotsLength = $this->slots->count();
			$this->durabilityLength = $this->durability->count();
		}
	}