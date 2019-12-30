<?php
	namespace App\Entity;

	use App\Entity\Strings\CharmStrings;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="charms")
	 *
	 * Class Charm
	 *
	 * @package App\Entity
	 */
	class Charm implements EntityInterface, TranslatableEntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\CharmRank", mappedBy="charm", orphanRemoval=true, cascade={"all"})
		 * @ORM\OrderBy(value={"level": "ASC"})
		 *
		 * @var Collection|Selectable|CharmRank[]
		 */
		private $ranks;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\CharmStrings",
		 *     mappedBy="charm",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|CharmStrings[]
		 */
		private $strings;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "ranks.length"
		 */
		private $ranksLength = 0;

		/**
		 * Charm constructor.
		 */
		public function __construct() {
			$this->ranks = new ArrayCollection();
			$this->strings = new ArrayCollection();
		}

		/**
		 * @return CharmRank[]|Collection|Selectable
		 */
		public function getRanks() {
			return $this->ranks;
		}

		/**
		 * @param int $level
		 *
		 * @return CharmRank|null
		 */
		public function getRank(int $level): ?CharmRank {
			$matches = $this->getRanks()->matching(
				Criteria::create()
					->where(Criteria::expr()->eq('level', $level))
					->setMaxResults(1)
			);

			if ($matches->count())
				return $matches->first();

			return null;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->ranksLength = $this->ranks->count();
		}

		/**
		 * @return CharmStrings[]|Collection|Selectable
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return CharmStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new CharmStrings($this, $language));

			return $strings;
		}
	}