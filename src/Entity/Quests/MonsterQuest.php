<?php
	namespace App\Entity\Quests;

	use App\Entity\Location;
	use App\Entity\Quest;
	use App\Game\Quest\QuestSubject;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 */
	class MonsterQuest extends Quest {
		/**
		 * {@inheritdoc}
		 */
		protected $subject = QuestSubject::MONSTER;

		/**
		 * @Assert\Count(min="1")
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Quests\MonsterQuestTarget",
		 *     mappedBy="quest",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|MonsterQuestTarget[]
		 */
		protected $targets;

		/**
		 * {@inheritdoc}
		 */
		public function __construct(Location $location, string $objective, string $type, string $rank, int $stars) {
			parent::__construct($location, $objective, $type, $rank, $stars);

			$this->targets = new ArrayCollection();
		}

		/**
		 * @return MonsterQuestTarget[]|Selectable|Collection
		 */
		public function getTargets(): Collection {
			return $this->targets;
		}
	}