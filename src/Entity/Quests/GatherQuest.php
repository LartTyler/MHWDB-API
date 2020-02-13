<?php
	namespace App\Entity\Quests;

	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Quest;
	use App\Game\Quest\QuestObjective;
	use App\Game\Quest\QuestSubject;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 */
	class GatherQuest extends Quest {
		/**
		 * {@inheritdoc}
		 */
		protected $subject = QuestSubject::ITEM;

		/**
		 * @Assert\NotNull()
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Item")
		 *
		 * @var Item|null
		 */
		private $item = null;

		/**
		 * @Assert\NotNull()
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true}, nullable=true)
		 *
		 * @var int|null
		 */
		private $amount = null;

		/**
		 * GatherQuest constructor.
		 *
		 * @param Location $location
		 * @param string   $type
		 * @param string   $rank
		 * @param int      $stars
		 */
		public function __construct(Location $location, string $type, string $rank, int $stars) {
			parent::__construct($location, QuestObjective::GATHER, $type, $rank, $stars);
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
	}