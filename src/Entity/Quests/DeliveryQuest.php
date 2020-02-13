<?php
	namespace App\Entity\Quests;

	use App\Entity\Quest;
	use App\Game\Quest\Objective;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 */
	class DeliveryQuest extends Quest {
		/**
		 * {@inheritdoc}
		 */
		protected $objective = Objective::DELIVER;

		/**
		 * @var int
		 */
		private $amount;

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