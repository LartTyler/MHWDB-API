<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

	use App\Entity\AttributableTrait;
	use App\Entity\WeaponAttackValues;
	use App\Entity\WeaponSharpness;

	class WeaponData {
		use AttributableTrait;

		/**
		 * @var WeaponSharpness
		 */
		protected $sharpness;

		/**
		 * @var WeaponAttackValues
		 */
		protected $attack;

		/**
		 * @var string|null
		 */
		protected $name = null;

		/**
		 * @var int|null
		 */
		protected $rarity = null;

		/**
		 * @var int[]
		 */
		protected $slots = [];

		/**
		 * @var string|null
		 */
		protected $craftingPrevious = null;

		/**
		 * @var array
		 */
		protected $craftingMaterials = [];

		/**
		 * @var array
		 */
		protected $upgradeMaterials = [];

		/**
		 * @var bool
		 */
		protected $craftable = false;

		/**
		 * @var Element[]
		 */
		protected $elements = [];

		/**
		 * WeaponData constructor.
		 */
		public function __construct() {
			$this->sharpness = new WeaponSharpness();
			$this->attack = new WeaponAttackValues();
		}

		/**
		 * @return string|null
		 */
		public function getName(): ?string {
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name) {
			$this->name = $name;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getRarity(): ?int {
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
		 * @return int[]
		 */
		public function getSlots(): array {
			return $this->slots;
		}

		/**
		 * @param int[] $slots
		 *
		 * @return $this
		 */
		public function setSlots(array $slots) {
			$this->slots = $slots;

			return $this;
		}

		/**
		 * @param int $rank
		 *
		 * @return $this
		 */
		public function addSlot(int $rank) {
			$this->slots[] = $rank;

			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getCraftingPrevious(): ?string {
			return $this->craftingPrevious;
		}

		/**
		 * @param null|string $craftingPrevious
		 *
		 * @return $this
		 */
		public function setCraftingPrevious(string $craftingPrevious) {
			$this->craftingPrevious = $craftingPrevious;

			return $this;
		}

		/**
		 * @return array
		 */
		public function getUpgradeMaterials(): array {
			return $this->upgradeMaterials;
		}

		/**
		 * @param array $upgradeMaterials
		 *
		 * @return $this
		 */
		public function setUpgradeMaterials(array $upgradeMaterials) {
			$this->upgradeMaterials = $upgradeMaterials;

			return $this;
		}

		/**
		 * @return array
		 */
		public function getCraftingMaterials(): array {
			return $this->craftingMaterials;
		}

		/**
		 * @param array $craftingMaterials
		 *
		 * @return $this
		 */
		public function setCraftingMaterials(array $craftingMaterials) {
			$this->craftingMaterials = $craftingMaterials;

			return $this;
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
		 * @return WeaponSharpness
		 */
		public function getSharpness(): WeaponSharpness {
			return $this->sharpness;
		}

		/**
		 * @return Element[]
		 */
		public function getElements(): array {
			return $this->elements;
		}

		/**
		 * @param Element $element
		 *
		 * @return $this
		 */
		public function setElement(Element $element) {
			if (!$element->getType() || $element->getDamage() === null)
				throw new \InvalidArgumentException('The provied element is incomplete');

			$this->elements[$element->getType()] = $element;

			return $this;
		}

		/**
		 * @return WeaponAttackValues
		 */
		public function getAttack(): WeaponAttackValues {
			return $this->attack;
		}
	}