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
	 * @ORM\InheritanceType("SINGLE_TABLE")
	 * @ORM\DiscriminatorColumn(name="targetType", type="string", length=12)
	 * @ORM\DiscriminatorMap(
	 *     "endemic life" = "App\Entity\Quest\EndemicLifeDeliveryQuest",
	 *     "object" = "App\Entity\Quest\ObjectDeliveryQuest"
	 * )
	 */
	class DeliveryQuest extends Quest {
		/**
		 * {@inheritdoc}
		 */
		protected $subject = QuestSubject::ENTITY;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Quest\DeliveryTarget", "values"})
		 *
		 * @var string|null
		 */
		protected $targetType = null;

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
		 * DeliveryQuest constructor.
		 *
		 * @param Location $location
		 * @param string   $type
		 * @param string   $rank
		 * @param int      $stars
		 */
		public function __construct(Location $location, string $type, string $rank, int $stars) {
			parent::__construct($location, QuestObjective::DELIVER, $type, $rank, $stars);

			assert($this->targetType !== null);
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