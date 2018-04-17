<?php
	namespace App\Entity;

	use App\Utility\StringUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class Decoration implements EntityInterface, SluggableInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var int
		 */
		private $slot;

		/**
		 * @var int
		 */
		private $rarity;

		/**
		 * @var Skill|null
		 */
		private $skill = null;

		/**
		 * Decoration constructor.
		 *
		 * @param string $name
		 * @param int    $slot
		 * @param int    $rarity
		 */
		public function __construct(string $name, int $slot, int $rarity) {
			$this->name = $name;
			$this->slot = $slot;
			$this->rarity = $rarity;

			$this->setSlug(StringUtil::toSlug($name));
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
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
		 * @return int
		 */
		public function getSlot(): int {
			return $this->slot;
		}

		/**
		 * @param int $slot
		 *
		 * @return $this
		 */
		public function setSlot(int $slot) {
			$this->slot = $slot;

			return $this;
		}

		/**
		 * @return Skill|null
		 */
		public function getSkill(): ?Skill {
			return $this->skill;
		}

		/**
		 * @param Skill $skill
		 *
		 * @return $this
		 */
		public function setSkill(Skill $skill) {
			$this->skill = $skill;

			return $this;
		}
	}