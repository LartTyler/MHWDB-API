<?php
	namespace App\Entity;

	use App\Game\Attribute;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;

	class Weapon implements EntityInterface, SluggableInterface {
		use EntityTrait;
		use SluggableTrait;
		use AttributableTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var string
		 */
		private $type;

		/**
		 * @var int
		 */
		private $rarity;

		/**
		 * @var Collection|Selectable|Slot[]
		 */
		private $slots;

		/**
		 * @var WeaponSharpness
		 */
		private $sharpness;

		/**
		 * @var Collection|Selectable|WeaponElement[]
		 */
		private $elements;

		/**
		 * @var WeaponAttackValues
		 */
		private $attack;

		/**
		 * @var WeaponCraftingInfo|null
		 */
		private $crafting = null;

		/**
		 * @var WeaponAssets|null
		 */
		private $assets = null;

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
			$this->sharpness = new WeaponSharpness();
			$this->attack = new WeaponAttackValues();
			$this->elements = new ArrayCollection();

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
		 * @return WeaponSharpness
		 */
		public function getSharpness(): WeaponSharpness {
			return $this->sharpness;
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
	}