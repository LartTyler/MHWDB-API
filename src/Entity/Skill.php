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
	 * @ORM\Table(name="skills")
	 *
	 * Class Skill
	 *
	 * @package App\Entity
	 */
	class Skill implements EntityInterface, SluggableInterface, LengthCachingEntityInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\SkillRank", mappedBy="skill", orphanRemoval=true, cascade={"all"})
		 *
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $ranks;

		/**
		 * @ORM\Column(type="text")
		 *
		 * @var string|null
		 */
		private $description = null;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "ranks.length"
		 */
		private $ranksLength = 0;

		/**
		 * Skill constructor.
		 *
		 * @param string $name
		 */
		public function __construct(string $name) {
			$this->name = $name;
			$this->ranks = new ArrayCollection();

			$this->updateSlug();
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

			$this->updateSlug();

			return $this;
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
		 * @return void
		 */
		protected function updateSlug(): void {
			$this->setSlug($this->getName());
		}

		/**
		 * @return null|string
		 */
		public function getDescription() {
			return $this->description;
		}

		/**
		 * @param null|string $description
		 *
		 * @return $this
		 */
		public function setDescription(?string $description) {
			$this->description = $description;
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->ranksLength = $this->ranks->count();
		}
	}