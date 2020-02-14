<?php
	namespace App\Entity;

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
		 * @ORM\ManyToOne(targetEntity="App\Entity\Quest", inversedBy="targets")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Quest
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
		 * @param Quest   $quest
		 * @param Monster $monster
		 * @param int     $amount
		 */
		public function __construct(Quest $quest, Monster $monster, int $amount) {
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