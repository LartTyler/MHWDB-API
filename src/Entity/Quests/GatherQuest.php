<?php
	namespace App\Entity\Quests;

	use App\Entity\Item;
	use App\Entity\Quest;
	use App\Game\Quest\Objective;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 */
	class GatherQuest extends Quest {
		/**
		 * {@inheritdoc}
		 */
		protected $objective = Objective::GATHER;

		/**
		 * @var Item
		 */
		private $item;

		/**
		 * @var int
		 */
		private $amount;

		/**
		 * GatherQuest constructor.
		 *
		 * @param Item   $item
		 * @param int    $amount
		 * @param string $type
		 * @param string $rank
		 * @param int    $stars
		 */
		public function __construct(Item $item, int $amount, string $type, string $rank, int $stars) {
			parent::__construct($type, $rank, $stars);

			$this->item = $item;
			$this->amount = $amount;
		}

		/**
		 * @return Item
		 */
		public function getItem(): Item {
			return $this->item;
		}

		/**
		 * @param Item $item
		 *
		 * @return $this
		 */
		public function setItem(Item $item) {
			$this->item = $item;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getAmount(): int {
			return $this->amount;
		}

		/**
		 * @param int $amount
		 *
		 * @return $this
		 */
		public function setAmount(int $amount) {
			$this->amount = $amount;

			return $this;
		}
	}