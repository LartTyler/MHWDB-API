<?php
	namespace App\Entity;

	use App\Entity\Strings\SkillRankStrings;
	use App\Game\Attribute;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="skill_ranks")
	 */
	class SkillRank implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="ranks")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Skill
		 */
		private $skill;

		/**
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $level;

		/**
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\SkillRankStrings",
		 *     mappedBy="rank",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|SkillRankStrings[]
		 */
		private $strings;

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
		 */
		public function __construct(Skill $skill, int $level) {
			$this->skill = $skill;
			$this->level = $level;

			$this->strings = new ArrayCollection();
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
				$this->setModifier($attribute, $amount);

			return $this;
		}

		/**
		 * @param string          $attribute
		 * @param string|int|bool $amount
		 *
		 * @return $this
		 * @see Attribute
		 */
		public function setModifier(string $attribute, $amount): SkillRank {
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
		 * @return Collection|Selectable|SkillRankStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return SkillRankStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new SkillRankStrings($this, $language));

			return $strings;
		}
	}