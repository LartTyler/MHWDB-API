<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;

	class Charm implements EntityInterface, SluggableInterface, LengthCachingEntityInterface {
		use EntityTrait;
		use SluggableTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var Collection|Selectable|CharmRank[]
		 */
		private $ranks;

		/**
		 * @var int
		 * @internal Used to allow API queries against "ranks.length"
		 */
		private $ranksLength = 0;

		/**
		 * Charm constructor.
		 *
		 * @param string $name
		 */
		public function __construct(string $name) {
			$this->name = $name;
			$this->ranks = new ArrayCollection();

			$this->setSlug($name);
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
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
	}