<?php
	namespace App\Entity\Quests;

	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\Quest;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	abstract class AbstractMonsterTargetQuest extends Quest {
		/**
		 * @ORM\ManyToMany(targetEntity="App\Entity\Monster")
		 * @ORM\JoinTable(name="quests_monster_targets")
		 *
		 * @var Collection|Selectable|Monster[]
		 */
		protected $monsters;

		/**
		 * AbstractMonsterTargetQuest constructor.
		 *
		 * @param Location $location
		 * @param string   $type
		 * @param string   $rank
		 * @param int      $stars
		 */
		public function __construct(Location $location, string $type, string $rank, int $stars) {
			parent::__construct($location, $type, $rank, $stars);

			$this->monsters = new ArrayCollection();
		}

		/**
		 * @return Monster[]|Collection|Selectable
		 */
		public function getMonsters(): Collection {
			return $this->monsters;
		}
	}