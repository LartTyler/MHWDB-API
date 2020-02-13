<?php
	namespace App\Entity\Quests;

	use App\Entity\Location;
	use App\Entity\Quest;
	use App\Game\Quest\QuestObjective;
	use App\Game\Quest\QuestSubject;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 */
	class DeliveryQuest extends Quest {
		/**
		 * {@inheritdoc}
		 */
		protected $subject = QuestSubject::ENTITY;

		/**
		 * @Assert\NotNull()
		 *
		 * @ORM\OneToOne(targetEntity="App\Entity\Quests\DeliveryQuestTarget", mappedBy="quest", fetch="EAGER")
		 *
		 * @var DeliveryQuestTarget
		 */
		private $target = null;

		/**
		 * DeliveryQuest constructor.
		 *
		 * @param Location $location
		 * @param string   $type
		 * @param string   $rank
		 * @param int      $stars
		 */
		public function __construct(Location $location, string $type, string $rank, int $stars) {
			parent::__construct($location, QuestObjective::DELIVER, $type, $rank, $stars);
		}

		/**
		 * @return DeliveryQuestTarget
		 */
		public function getTarget(): DeliveryQuestTarget {
			return $this->target;
		}

		/**
		 * @param DeliveryQuestTarget $target
		 *
		 * @return $this
		 */
		public function setTarget(DeliveryQuestTarget $target) {
			$this->target = $target;

			return $this;
		}
	}