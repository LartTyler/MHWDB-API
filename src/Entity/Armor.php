<?php
	namespace App\Entity;

	use App\Entity\Strings\ArmorStrings;
	use App\Game\ArmorType;
	use App\Game\Rank;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\ArmorRepository")
	 * @ORM\Table(
	 *     name="armor",
	 *     indexes={
	 *         @ORM\Index(columns={"type"})
	 *     }
	 * )
	 *
	 * Class Armor
	 *
	 * @package App\Entity
	 */
	class Armor implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;
		use AttributableTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\ArmorType", "all"})
		 *
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see ArmorType
		 */
		private $type;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Rank", "values"})
		 *
		 * @ORM\Column(type="string", length=16)
		 *
		 * @var string
		 * @see Rank
		 */
		private $rank;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $rarity;

		/**
		 * @ORM\Embedded(class="App\Entity\Resistances", columnPrefix="resist_")
		 *
		 * @var Resistances
		 */
		private $resistances;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\Embedded(class="App\Entity\ArmorDefenseValues", columnPrefix="defense_")
		 *
		 * @var ArmorDefenseValues
		 */
		private $defense;

		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\SkillRank")
		 * @ORM\JoinTable(name="armor_skill_ranks")
		 *
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $skills;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\ArmorSlot", mappedBy="armor", orphanRemoval=true, cascade={"all"})
		 *
		 * @var Collection|Selectable|ArmorSlot[]
		 */
		private $slots;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\ArmorStrings",
		 *     mappedBy="armor",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|ArmorStrings[]
		 */
		private $strings;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\ArmorSet", inversedBy="pieces")
		 *
		 * @var ArmorSet|null
		 */
		private $armorSet = null;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToOne(targetEntity="App\Entity\ArmorAssets", orphanRemoval=true, cascade={"all"})
		 *
		 * @var ArmorAssets|null
		 */
		private $assets = null;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToOne(targetEntity="App\Entity\ArmorCraftingInfo", orphanRemoval=true, cascade={"all"})
		 *
		 * @var ArmorCraftingInfo|null
		 */
		private $crafting = null;

		/**
		 * Armor constructor.
		 *
		 * @param string $type
		 * @param string $rank
		 * @param int    $rarity
		 */
		public function __construct(string $type, string $rank, int $rarity) {
			$this->type = $type;
			$this->rank = $rank;
			$this->rarity = $rarity;

			$this->resistances = new Resistances();
			$this->defense = new ArmorDefenseValues();

			$this->skills = new ArrayCollection();
			$this->slots = new ArrayCollection();
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
		 * @return SkillRank[]|Collection|Selectable
		 */
		public function getSkills() {
			return $this->skills;
		}

		/**
		 * @return ArmorSlot[]|Collection|Selectable
		 */
		public function getSlots() {
			return $this->slots;
		}

		/**
		 * @return string
		 */
		public function getRank(): string {
			return $this->rank;
		}

		/**
		 * @param string $rank
		 *
		 * @return $this
		 */
		public function setRank(string $rank) {
			$this->rank = $rank;

			return $this;
		}

		/**
		 * @return ArmorSet|null
		 */
		public function getArmorSet(): ?ArmorSet {
			return $this->armorSet;
		}

		/**
		 * @param ArmorSet|null $armorSet
		 *
		 * @return $this
		 */
		public function setArmorSet(?ArmorSet $armorSet) {
			if ($armorSet === null && $this->armorSet)
				$this->armorSet->getPieces()->removeElement($this);

			$this->armorSet = $armorSet;

			if ($armorSet && !$armorSet->getPieces()->contains($this))
				$armorSet->getPieces()->add($this);

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
		public function setRarity(int $rarity) {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return Resistances
		 */
		public function getResistances(): Resistances {
			return $this->resistances;
		}

		/**
		 * @return ArmorDefenseValues
		 */
		public function getDefense(): ArmorDefenseValues {
			return $this->defense;
		}

		/**
		 * @return ArmorAssets|null
		 */
		public function getAssets(): ?ArmorAssets {
			return $this->assets;
		}

		/**
		 * @param ArmorAssets|null $assets
		 *
		 * @return $this
		 */
		public function setAssets(?ArmorAssets $assets) {
			$this->assets = $assets;

			return $this;
		}

		/**
		 * @return ArmorCraftingInfo|null
		 */
		public function getCrafting(): ?ArmorCraftingInfo {
			return $this->crafting;
		}

		/**
		 * @param ArmorCraftingInfo $crafting
		 *
		 * @return $this
		 */
		public function setCrafting(ArmorCraftingInfo $crafting) {
			$this->crafting = $crafting;

			return $this;
		}

		/**
		 * @return ArmorStrings[]|Collection|Selectable
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return ArmorStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new ArmorStrings($this, $language));

			return $strings;
		}
	}