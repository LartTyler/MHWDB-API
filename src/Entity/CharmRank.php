<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;

	class CharmRank implements EntityInterface {
		use EntityTrait;

		/**
		 * @var Charm
		 */
		private $charm;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var int
		 */
		private $level;

		/**
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $skills;

		/**
		 * @var CharmRankCraftingInfo|null
		 */
		private $crafting = null;

		/**
		 * CharmRank constructor.
		 *
		 * @param Charm  $charm
		 * @param string $name
		 * @param int    $level
		 */
		public function __construct(Charm $charm, string $name, int $level) {
			$this->charm = $charm;
			$this->name = $name;
			$this->level = $level;
			$this->skills = new ArrayCollection();
		}

		/**
		 * @return Charm
		 */
		public function getCharm(): Charm {
			return $this->charm;
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
		public function setName(string $name) {
			$this->name = $name;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getLevel(): int {
			return $this->level;
		}

		/**
		 * @param int $level
		 *
		 * @return $this
		 */
		public function setLevel(int $level) {
			$this->level = $level;

			return $this;
		}

		/**
		 * @return SkillRank[]|Collection|Selectable
		 */
		public function getSkills() {
			return $this->skills;
		}

		/**
		 * @return CharmRankCraftingInfo|null
		 */
		public function getCrafting(): ?CharmRankCraftingInfo {
			return $this->crafting;
		}

		/**
		 * @param CharmRankCraftingInfo $crafting
		 *
		 * @return $this
		 */
		public function setCrafting(CharmRankCraftingInfo $crafting) {
			$this->crafting = $crafting;

			return $this;
		}
	}