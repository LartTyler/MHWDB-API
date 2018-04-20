<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;

	class Armor implements EntityInterface {
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
		 * @var string
		 */
		private $rank;

		/**
		 * @var int
		 */
		private $rarity;

		/**
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $skills;

		/**
		 * @var Collection|Selectable|Slot[]
		 */
		private $slots;

		/**
		 * @var ArmorSet|null
		 */
		private $armorSet = null;

		/**
		 * @var ArmorAssets|null
		 */
		private $assets = null;

		/**
		 * Armor constructor.
		 *
		 * @param string $name
		 * @param string $type
		 * @param string $rank
		 * @param int    $rarity
		 */
		public function __construct(string $name, string $type, string $rank, int $rarity) {
			$this->name = $name;
			$this->type = $type;
			$this->rank = $rank;
			$this->rarity = $rarity;
			$this->skills = new ArrayCollection();
			$this->slots = new ArrayCollection();

			$this->setSlug($name);
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @return SkillRank[]|Collection|Selectable
		 */
		public function getSkills() {
			return $this->skills;
		}

		/**
		 * @return Slot[]|Collection|Selectable
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
	}