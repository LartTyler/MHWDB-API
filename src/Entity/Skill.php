<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;

	class Skill implements EntityInterface, SluggableInterface, LengthCachingEntityInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $ranks;

		/**
		 * @var string|null
		 */
		private $description = null;

		/**
		 * @var int
		 * @internal Used to allow API queries against "ranks.length"
		 */
		private $ranksLength = 0;

		/**
		 * Skill constructor.
		 *
		 * @param string $name
		 */
		public function __construct($name) {
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