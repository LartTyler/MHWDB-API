<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="armor_set_bonuses")
	 *
	 * Class ArmorSetBonus
	 *
	 * @package App\Entity
	 */
	class ArmorSetBonus implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\ArmorSetBonusRank",
		 *     mappedBy="bonus",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var ArmorSetBonusRank[]|Collection|Selectable
		 */
		private $ranks;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "ranks.length"
		 */
		private $ranksLength = 0;

		/**
		 * ArmorSetBonus constructor.
		 *
		 * @param string $name
		 */
		public function __construct(string $name) {
			$this->name = $name;

			$this->ranks = new ArrayCollection();
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name) {
			$this->name = $name;

			return $this;
		}

		/**
		 * @return ArmorSetBonusRank[]|Collection|Selectable
		 */
		public function getRanks() {
			return $this->ranks;
		}

		/**
		 * @param int $pieces
		 *
		 * @return ArmorSetBonusRank|null
		 */
		public function getRank(int $pieces): ?ArmorSetBonusRank {
			$criteria = Criteria::create()
				->where(Criteria::expr()->eq('pieces', $pieces))
				->setMaxResults(1);

			$matching = $this->getRanks()->matching($criteria);

			if (!$matching->count())
				return null;

			return $matching->first();
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->ranksLength = $this->ranks->count();
		}
	}