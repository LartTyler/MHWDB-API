<?php
	namespace App\Entity\Quests;

	use App\Entity\EntityTrait;
	use App\Entity\Monster;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(name="quest_monster_targets")
	 */
	class MonsterQuestTarget implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Quests\MonsterQuest", inversedBy="targets")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var MonsterQuest
		 */
		private $quest;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Monster", fetch="EAGER")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Monster
		 */
		private $monster;

		/**
		 * @Assert\Range(min="1")
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $amount;

		/**
		 * MonsterQuestTarget constructor.
		 *
		 * @param MonsterQuest $quest
		 * @param Monster      $monster
		 * @param int          $amount
		 */
		public function __construct(MonsterQuest $quest, Monster $monster, int $amount) {
			$this->quest = $quest;
			$this->monster = $monster;
			$this->amount = $amount;
		}

		/**
		 * @return Monster
		 */
		public function getMonster(): Monster {
			return $this->monster;
		}

		/**
		 * @return int
		 */
		public function getAmount(): int {
			return $this->amount;
		}
	}