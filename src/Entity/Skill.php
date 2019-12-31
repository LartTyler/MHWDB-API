<?php
	namespace App\Entity;

	use App\Entity\Strings\SkillStrings;
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
	 * @ORM\Table(name="skills")
	 */
	class Skill implements EntityInterface, TranslatableEntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\SkillRank", mappedBy="skill", orphanRemoval=true, cascade={"all"})
		 *
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $ranks;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\SkillStrings",
		 *     mappedBy="skill",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|SkillStrings[]
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
		 * Skill constructor.
		 */
		public function __construct() {
			$this->ranks = new ArrayCollection();
			$this->strings = new ArrayCollection();
		}

		/**
		 * @return Collection|Selectable|SkillRank[]
		 */
		public function getRanks(): Collection {
			return $this->ranks;
		}

		/**
		 * @param int $level
		 *
		 * @return SkillRank|null
		 */
		public function getRank(int $level): ?SkillRank {
			$criteria = Criteria::create()
				->where(Criteria::expr()->eq('level', $level))
				->setMaxResults(1);

			$matched = $this->getRanks()->matching($criteria);

			if ($matched->count())
				return $matched->first();

			return null;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->ranksLength = $this->ranks->count();
		}

		/**
		 * @return Collection|Selectable|SkillStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return SkillStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new SkillStrings($this, $language));

			return $strings;
		}
	}