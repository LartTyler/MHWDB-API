<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="quest_rewards",
	 *     uniqueConstraints={
	 *         @ORM\UniqueConstraint(columns={"quest_id", "item_id"})
	 *     }
	 * )
	 */
	class QuestReward implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Quest", inversedBy="rewards")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Quest
		 */
		private $quest;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Item")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Item
		 */
		private $item;

		/**
		 * @Assert\Valid()
		 * @Assert\Count(min="1")
		 *
		 * @ORM\ManyToMany(targetEntity="App\Entity\RewardCondition", cascade={"all"}, orphanRemoval=true)
		 * @ORM\JoinTable(name="quest_reward_conditions")
		 *
		 * @var Collection|Selectable|RewardCondition[]
		 */
		private $conditions;

		/**
		 * QuestReward constructor.
		 *
		 * @param Quest $quest
		 * @param Item  $item
		 */
		public function __construct(Quest $quest, Item $item) {
			$this->quest = $quest;
			$this->item = $item;

			$this->conditions = new ArrayCollection();
		}

		/**
		 * @return Item
		 */
		public function getItem(): Item {
			return $this->item;
		}

		/**
		 * @return RewardCondition[]|Collection|Selectable
		 */
		public function getConditions() {
			return $this->conditions;
		}
	}