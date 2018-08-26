<?php
	namespace App\Entity;

	use App\Game\Attribute;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="skill_ranks")
	 *
	 * Class SkillRank
	 *
	 * @package App\Entity
	 */
	class SkillRank implements EntityInterface, SluggableInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="ranks")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Skill
		 */
		private $skill;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $level;

		/**
		 * @ORM\Column(type="text")
		 *
		 * @var string
		 */
		private $description;

		/**
		 * @ORM\Column(type="json")
		 *
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