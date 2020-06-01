<?php
	namespace App\Entity;

	use App\Entity\Strings\QuestStrings;
	use App\Game\Quest\DeliveryType;
	use App\Game\Quest\QuestObjective;
	use App\Game\Quest\QuestType;
	use App\Game\Rank;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\QuestRepository")
	 * @ORM\Table(name="quests")
	 */
	class Quest implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Location")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Location
		 */
		private $location;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Quest\QuestObjective", "values"})
		 *
		 * @ORM\Column(type="string", length=18)
		 *
		 * @var string
		 * @see QuestObjective
		 */
		private $objective;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Quest\QuestType", "values"})
		 *
		 * @ORM\Column(type="string", length=18)
		 *
		 * @var string
		 * @see QuestType
		 */
		private $type;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Rank", "values"})
		 *
		 * @ORM\Column(type="string", length=6)
		 *
		 * @var string
		 * @see Rank
		 */
		private $rank;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $stars;

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
		private $strings;

		/**
		 * Used by {@see QuestObjective::HUNT}, {@see QuestObjective::CAPTURE}, and {@see QuestObjective::SLAY}
		 * objectives to store quest target information.
		 *
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\MonsterQuestTarget",
		 *     mappedBy="quest",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|MonsterQuestTarget[]
		 */
		private $targets;

		/**
		 * @Assert\Valid()
		 * @Assert\Count(min="1")
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\QuestReward", mappedBy="quest", cascade={"all"}, orphanRemoval=true)
		 *
		 * @var Collection|Selectable|QuestReward[]
		 */
		private $rewards;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\WorldEvent", mappedBy="quest", orphanRemoval=true, cascade={"all"})
		 *
		 * @var WorldEvent[]|Collection|Selectable
		 */
		private $events;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $timeLimit = 3000;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $maxHunters = 4;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $maxFaints = 3;

		/**
		 * Used by {@see QuestObjective::DELIVER} objectives to indicate the delivery targets classification.
		 *
		 * @Assert\Choice(callback={"App\Game\Quest\DeliveryType", "values"})
		 *
		 * @ORM\Column(type="string", length=12, nullable=true)
		 *
		 * @var string|null
		 * @see DeliveryType
		 */
		private $deliveryType = null;

		/**
		 * Used by {@see QuestObjective::DELIVER} and {@see QuestObjective::GATHER} to indicate the quantity of the
		 * target that must be delivered.
		 *
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true}, nullable=true)
		 *
		 * @var int|null
		 */
		private $amount = null;

		/**
		 * Used by {@see QuestObjective::DELIVER} + {@see DeliveryType::ENDEMIC_LIFE} to indicate the target endemic
		 * life.
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\EndemicLife", fetch="EAGER")
		 *
		 * @var EndemicLife|null
		 */
		private $endemicLife = null;

		/**
		 * Used by {@see QuestObjective::GATHER} to indicate the item that must be gathered.
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Item", fetch="EAGER")
		 *
		 * @var Item|null
		 */
		private $item = null;

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
			$this->location = $location;
			$this->objective = $objective;
			$this->type = $type;
			$this->rank = $rank;
			$this->stars = $stars;

			$this->strings = new ArrayCollection();
			$this->targets = new ArrayCollection();
			$this->rewards = new ArrayCollection();
			$this->events = new ArrayCollection();
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
		 * @return MonsterQuestTarget[]|Collection|Selectable
		 */
		public function getTargets() {
			return $this->targets;
		}

		/**
		 * @return string|null
		 */
		public function getDeliveryType(): ?string {
			return $this->deliveryType;
		}

		/**
		 * @param string|null $deliveryType
		 *
		 * @return $this
		 */
		public function setDeliveryType(?string $deliveryType) {
			$this->deliveryType = $deliveryType;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getAmount(): ?int {
			return $this->amount;
		}

		/**
		 * @param int|null $amount
		 *
		 * @return $this
		 */
		public function setAmount(?int $amount) {
			$this->amount = $amount;

			return $this;
		}

		/**
		 * @return EndemicLife|null
		 */
		public function getEndemicLife(): ?EndemicLife {
			return $this->endemicLife;
		}

		/**
		 * @param EndemicLife|null $endemicLife
		 *
		 * @return $this
		 */
		public function setEndemicLife(?EndemicLife $endemicLife) {
			$this->endemicLife = $endemicLife;

			return $this;
		}

		/**
		 * @return Item|null
		 */
		public function getItem(): ?Item {
			return $this->item;
		}

		/**
		 * @param Item|null $item
		 *
		 * @return $this
		 */
		public function setItem(?Item $item) {
			$this->item = $item;

			return $this;
		}

		/**
		 * @return QuestReward[]|Collection|Selectable
		 */
		public function getRewards(): Collection {
			return $this->rewards;
		}

		/**
		 * @param Item $item
		 *
		 * @return QuestReward|null
		 */
		public function getRewardForItem(Item $item): ?QuestReward {
			return $this->getRewards()->matching(
				Criteria::create()
					->where(Criteria::expr()->eq('item', $item))
					->setMaxResults(1)
			)->first() ?: null;
		}

		/**
		 * @return Collection|Selectable|WorldEvent[]
		 */
		public function getEvents(): Collection {
			return $this->events;
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