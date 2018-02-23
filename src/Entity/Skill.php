<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;

	class Skill implements EntityInterface {
		use EntityTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var Collection|Selectable|SkillRank[]
		 */
		private $ranks;

		/**
		 * Skill constructor.
		 *
		 * @param string   $name
		 */
		public function __construct($name) {
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
		public function getRank(int $level) {
			$criteria = Criteria::create()
				->where(Criteria::expr()->eq('level', $level))
				->setMaxResults(1);

			$matched = $this->getRanks()->matching($criteria);

			if ($matched->count())
				return $matched->first();

			return null;
		}
	}