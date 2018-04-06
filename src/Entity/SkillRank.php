<?php
	namespace App\Entity;

	use App\Game\Attribute;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class SkillRank implements EntityInterface, SluggableInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @var Skill
		 */
		private $skill;

		/**
		 * @var int
		 */
		private $level;

		/**
		 * @var string
		 */
		private $description;

		/**
		 * @var array
		 */
		private $modifiers = [];

		/**
		 * SkillRank constructor.
		 *
		 * @param Skill  $skill
		 * @param int    $level
		 * @param string $description
		 */
		public function __construct(Skill $skill, int $level, string $description) {
			$this->skill = $skill;
			$this->level = $level;
			$this->description = $description;

			$this->updateSlug();
		}

		/**
		 * @return Skill
		 */
		public function getSkill(): Skill {
			return $this->skill;
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

			$this->updateSlug();

			return $this;
		}

		/**
		 * @return string
		 */
		public function getDescription(): string {
			return $this->description;
		}

		/**
		 * @param string $description
		 *
		 * @return $this
		 */
		public function setDescription(string $description) {
			$this->description = $description;

			return $this;
		}

		/**
		 * @return array
		 */
		public function getModifiers(): array {
			return $this->modifiers;
		}

		/**
		 * @param array $modifiers
		 *
		 * @return $this
		 */
		public function setModifiers(array $modifiers): SkillRank {
			$this->modifiers = [];

			foreach ($modifiers as $attribute => $amount)
				$this->setModifer($attribute, $amount);

			return $this;
		}

		/**
		 * @param string          $attribute
		 * @param string|int|bool $amount
		 *
		 * @return $this
		 * @see Attribute
		 */
		public function setModifer(string $attribute, $amount): SkillRank {
			$this->modifiers[$attribute] = $amount;

			return $this;
		}

		/**
		 * @param string $attribute
		 *
		 * @return string|int|bool
		 */
		public function getModifier(string $attribute) {
			if (isset($this->modifiers[$attribute]))
				return $this->modifiers[$attribute];

			return 0;
		}

		/**
		 * @return void
		 */
		protected function updateSlug(): void {
			$this->setSlug($this->getSkill()->getName() . '-rank-' . $this->getLevel());
		}
	}