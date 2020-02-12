<?php
	namespace App\Entity\Quests;

	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Quest;
	use App\Game\Quest\Objective;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 */
	class GatherQuest extends Quest {
		/**
		 * {@inheritdoc}
		 */
		protected $objective = Objective::GATHER;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\Item")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Item
		 */
		private $item;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $amount;

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