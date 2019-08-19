<?php
	namespace App\Entity;

	use App\Game\BowCoatingType;
	use App\Game\Elderseal;
	use App\Game\WeaponType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

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
	class Weapon implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;
		use AttributableTrait;

		/**
		 * @Assert\NotBlank()
		 *
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\WeaponType", "all"})
		 *
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see WeaponType
		 */
		private $type;

		/**
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $rarity;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\WeaponSlot", mappedBy="weapon", orphanRemoval=true, cascade={"all"})
		 *
		 * @var Collection|Selectable|WeaponSlot[]
		 */
		private $slots;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\ManyToMany(targetEntity="App\Entity\WeaponSharpness", orphanRemoval=true, cascade={"all"})
		 * @ORM\JoinTable(name="weapon_durability")
		 *
		 * @var Collection|Selectable|WeaponSharpness[]
		 */
		private $durability;

		/**
		 * @Assert\Valid()
		 *
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
		 * @Assert\Valid()
		 *
		 * @ORM\Embedded(class="App\Entity\WeaponAttackValues", columnPrefix="attack_")
		 *
		 * @var WeaponAttackValues
		 */
		private $attack;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="Ammo",
		 *     mappedBy="weapon",
		 *     cascade={"all"},
		 *     orphanRemoval=true
		 * )
		 *
		 * @var Collection|Selectable|Ammo[]
		 */
		private $ammo;

		/**
		 * @Assert\Choice(choices=App\Game\Elderseal::ALL)
		 *
		 * @ORM\Column(type="string", length=16, nullable=true)
		 *
		 * @var string|null
		 * @see Elderseal
		 */
		private $elderseal = null;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToOne(targetEntity="App\Entity\Phial", inversedBy="weapon", cascade={"all"}, orphanRemoval=true)
		 * @ORM\JoinColumn()
		 *
		 * @var Phial|null
		 */
		private $phial = null;

		/**
		 * @Assert\All({
		 *     @Assert\Choice(callback={"App\Game\BowCoatingType", "all"})
		 * })
		 *
		 * @ORM\Column(type="json")
		 *
		 * @var string[]
		 * @see BowCoatingType
		 */
		private $coatings = [];

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToOne(targetEntity="App\Entity\WeaponCraftingInfo", orphanRemoval=true, cascade={"all"})
		 *
		 * @var WeaponCraftingInfo|null
		 */
		private $crafting = null;

		/**
		 * @Assert\Valid()
		 *
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
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "ammo.length"
		 */
		private $ammoLength = 0;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "coatings.length"
		 */
		private $coatingsLength = 0;

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

			$this->attack = new WeaponAttackValues();

			$this->slots = new ArrayCollection();
			$this->elements = new ArrayCollection();
			$this->durability = new ArrayCollection();
			$this->ammo = new ArrayCollection();
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

			return $this;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @param string $type
		 *
		 * @return $this
		 */
		public function setType(string $type) {
			$this->type = $type;

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
		public function setRarity(int $rarity): Weapon {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getElderseal(): ?string {
			return $this->elderseal;
		}

		/**
		 * @param string|null $elderseal
		 *
		 * @return $this
		 */
		public function setElderseal(?string $elderseal) {
			$this->elderseal = $elderseal;

			return $this;
		}

		/**
		 * @return WeaponSlot[]|Collection|Selectable
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
				Criteria::create()->where(Criteria::expr()->eq('type', strtolower($element)))
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
		 * @return Phial|null
		 */
		public function getPhial(): ?Phial {
			return $this->phial;
		}

		/**
		 * @param Phial|null $phial
		 *
		 * @return $this
		 */
		public function setPhial(?Phial $phial) {
			$this->phial = $phial;

			return $this;
		}

		/**
		 * @return Ammo[]|Collection|Selectable
		 */
		public function getAmmo() {
			return $this->ammo;
		}

		/**
		 * @param string $type
		 *
		 * @return Ammo|null
		 */
		public function getAmmoByType(string $type): ?Ammo {
			$criteria = Criteria::create()
				->where(Criteria::expr()->eq('type', $type));

			$matched = $this->getAmmo()->matching($criteria);

			if (!$matched->count())
				return null;

			return $matched->first();
		}

		/**
		 * @return string[]
		 * @see BowCoatingType
		 */
		public function getCoatings(): array {
			return $this->coatings;
		}

		/**
		 * @param string[] $coatings
		 *
		 * @return $this
		 * @see BowCoatingType
		 */
		public function setCoatings(array $coatings) {
			$this->coatings = $coatings;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->elementsLength = $this->elements->count();
			$this->slotsLength = $this->slots->count();
			$this->durabilityLength = $this->durability->count();
			$this->ammoLength = $this->ammo->count();
			$this->coatingsLength = sizeof($this->coatings);
		}
	}