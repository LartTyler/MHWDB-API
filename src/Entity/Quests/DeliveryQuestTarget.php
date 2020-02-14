<?php
	namespace App\Entity\Quests;

	use App\Entity\EntityTrait;
	use App\Game\Quest\DeliveryType;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="quest_delivery_targets")
	 * @ORM\InheritanceType("SINGLE_TABLE")
	 * @ORM\DiscriminatorColumn(name="deliveryType", type="string", length=12)
	 * @ORM\DiscriminatorMap({
	 *     "endemic life" = "App\Entity\Quests\DeliveryQuestEndemicLifeTarget",
	 *     "object" = "App\Entity\Quests\DeliveryQuestObjectTarget"
	 * })
	 */
	abstract class DeliveryQuestTarget implements EntityInterface {
		use EntityTrait;

		/**
		 * @Assert\Choice(callback={"App\Game\Quest\DeliveryType", "values"})
		 *
		 * @var string|null
		 * @see DeliveryType
		 */
		protected $deliveryType = null;

		/**
		 * @ORM\OneToOne(targetEntity="App\Entity\Quests\DeliveryQuest", inversedBy="target")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var DeliveryQuest
		 */
		protected $quest;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		protected $amount;

		/**
		 * DeliveryQuestTarget constructor.
		 *
		 * @param DeliveryQuest $quest
		 * @param int           $amount
		 */
		public function __construct(DeliveryQuest $quest, int $amount) {
			assert($this->deliveryType !== null);

			$this->quest = $quest;
			$this->amount = $amount;
		}

		/**
		 * @return string|null
		 */
		public function getDeliveryType(): ?string {
			return $this->deliveryType;
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