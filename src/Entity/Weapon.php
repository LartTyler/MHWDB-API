<?php
	namespace App\Entity;

	use App\Entity\Strings\WeaponStrings;
	use App\Game\BowCoatingType;
	use App\Game\BowgunDeviation;
	use App\Game\BowgunSpecialAmmo;
	use App\Game\DamageType;
	use App\Game\Elderseal;
	use App\Game\InsectGlaiveBoostType;
	use App\Game\WeaponType;
	use App\Localization\TranslatableEntityInterface;
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
	class Weapon implements EntityInterface, TranslatableEntityInterface, LengthCachingEntityInterface {
		use EntityTrait;
		use AttributableTrait;

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
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\WeaponStrings",
		 *     mappedBy="weapon",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|WeaponStrings[]
		 */
		private $strings;

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
		 * @Assert\Choice(callback={"App\Game\BowgunSpecialAmmo", "all"})
		 *
		 * @ORM\Column(type="string", length=32, nullable=true)
		 *
		 * @var string|null
		 * @see BowgunSpecialAmmo
		 */
		private $specialAmmo = null;

		/**
		 * @Assert\Choice(callback={"App\Game\BowgunDeviation", "all"})
		 *
		 * @ORM\Column(type="string", length=32, nullable=true)
		 *
		 * @var string|null
		 * @see BowgunDeviation
		 */
		private $deviation = null;

		/**
		 * @Assert\Choice(callback={"App\Game\InsectGlaiveBoostType", "all"})
		 *
		 * @ORM\Column(type="string", length=32, nullable=true)
		 *
		 * @var string|null
		 * @see InsectGlaiveBoostType
		 */
		private $boostType = null;

		/**
		 * @Assert\NotNull()
		 * @Assert\Choice(callback={"App\Game\DamageType", "all"})
		 *
		 * @ORM\Column(type="string", length=32, nullable=true)
		 *
		 * @var string|null
		 * @see DamageType
		 */
		private $damageType = null;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToOne(targetEntity="App\Entity\Shelling", mappedBy="weapon", orphanRemoval=true, cascade={"all"})
		 *
		 * @var Shelling|null
		 */
		private $shelling = null;

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
		 * @param string $type
		 * @param int    $rarity
		 */
		public function __construct(string $type, int $rarity) {
			$this->type = $type;
			$this->rarity = $rarity;

			$this->attack = new WeaponAttackValues();

			$this->slots = new ArrayCollection();
			$this->elements = new ArrayCollection();
			$this->durability = new ArrayCollection();
			$this->ammo = new ArrayCollection();
			$this->strings = new ArrayCollection();
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
		 * @return string|null
		 */
		public function getSpecialAmmo(): ?string {
			return $this->specialAmmo;
		}

		/**
		 * @param string|null $specialAmmo
		 *
		 * @return $this
		 */
		public function setSpecialAmmo(?string $specialAmmo) {
			$this->specialAmmo = $specialAmmo;

			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getDeviation(): ?string {
			return $this->deviation;
		}

		/**
		 * @param string|null $deviation
		 *
		 * @return $this
		 */
		public function setDeviation(?string $deviation) {
			$this->deviation = $deviation;

			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getBoostType(): ?string {
			return $this->boostType;
		}

		/**
		 * @param string|null $boostType
		 *
		 * @return $this
		 */
		public function setBoostType(?string $boostType) {
			$this->boostType = $boostType;

			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getDamageType(): ?string {
			return $this->damageType;
		}

		/**
		 * @param string|null $damageType
		 *
		 * @return $this
		 */
		public function setDamageType(?string $damageType) {
			$this->damageType = $damageType;

			return $this;
		}

		/**
		 * @return Shelling|null
		 */
		public function getShelling(): ?Shelling {
			return $this->shelling;
		}

		/**
		 * @param Shelling|null $shelling
		 *
		 * @return $this
		 */
		public function setShelling(?Shelling $shelling) {
			$this->shelling = $shelling;

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

		/**
		 * @return Collection|Selectable|WeaponStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return WeaponStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new WeaponStrings($this, $language));

			return $strings;
		}
	}