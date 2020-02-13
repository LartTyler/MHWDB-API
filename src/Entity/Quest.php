<?php
	namespace App\Entity;

	use App\Entity\Strings\QuestStrings;
	use App\Game\Quest\QuestObjective;
	use App\Game\Quest\QuestType;
	use App\Game\Rank;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="quests")
	 * @ORM\InheritanceType("SINGLE_TABLE")
	 * @ORM\DiscriminatorColumn(name="subject", type="string", length=7)
	 * @ORM\DiscriminatorMap(
	 *     "item" = "App\Entity\Quests\GatherQuest",
	 *     "monster" = "App\Entity\Quests\MonsterQuest"
	 * )
	 */
	abstract class Quest implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Quest\QuestSubject", "values"})
		 *
		 * @var string|null
		 */
		protected $subject = null;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Location")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Location
		 */
		protected $location;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Quest\QuestObjective", "values"})
		 *
		 * @ORM\Column(type="string", length=18)
		 *
		 * @var string
		 * @see QuestObjective
		 */
		protected $objective;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Quest\QuestType", "values"})
		 *
		 * @var string
		 * @see QuestType
		 */
		protected $type;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Rank", "values"})
		 *
		 * @ORM\Column(type="string", length=6)
		 *
		 * @var string
		 * @see Rank
		 */
		protected $rank;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		protected $stars;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\QuestStrings",
		 *     mappedBy="quest",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|QuestStrings[]
		 */
		protected $strings;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		protected $timeLimit = 3000;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		protected $maxHunters = 4;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		protected $maxFaints = 3;

		/**
		 * Quest constructor.
		 *
		 * @param Location $location
		 * @param string   $objective
		 * @param string   $type
		 * @param string   $rank
		 * @param int      $stars
		 */
		public function __construct(Location $location, string $objective, string $type, string $rank, int $stars) {
			assert($this->objective !== null);

			$this->location = $location;
			$this->objective = $objective;
			$this->type = $type;
			$this->rank = $rank;
			$this->stars = $stars;

			$this->strings = new ArrayCollection();
		}

		/**
		 * @return string|null
		 */
		public function getSubject(): ?string {
			return $this->subject;
		}

		/**
		 * @return Location
		 */
		public function getLocation(): Location {
			return $this->location;
		}

		/**
		 * @param Location $location
		 *
		 * @return $this
		 */
		public function setLocation(Location $location) {
			$this->location = $location;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getObjective(): string {
			return $this->objective;
		}

		/**
		 * @param string $objective
		 *
		 * @return $this
		 */
		public function setObjective(string $objective) {
			$this->objective = $objective;

			return $this;
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
		 * @return int
		 */
		public function getStars(): int {
			return $this->stars;
		}

		/**
		 * @param int $stars
		 *
		 * @return $this
		 */
		public function setStars(int $stars) {
			$this->stars = $stars;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getTimeLimit(): int {
			return $this->timeLimit;
		}

		/**
		 * @param int $timeLimit
		 *
		 * @return $this
		 */
		public function setTimeLimit(int $timeLimit) {
			$this->timeLimit = $timeLimit;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getMaxHunters(): int {
			return $this->maxHunters;
		}

		/**
		 * @param int $maxHunters
		 *
		 * @return $this
		 */
		public function setMaxHunters(int $maxHunters) {
			$this->maxHunters = $maxHunters;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getMaxFaints(): int {
			return $this->maxFaints;
		}

		/**
		 * @param int $maxFaints
		 *
		 * @return $this
		 */
		public function setMaxFaints(int $maxFaints) {
			$this->maxFaints = $maxFaints;

			return $this;
		}

		/**
		 * @return Collection|Selectable|QuestStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return QuestStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new QuestStrings($this, $language));

			return $strings;
		}
	}