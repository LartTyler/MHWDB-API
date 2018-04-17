<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;

	class ArmorSetBonus implements EntityInterface {
		use EntityTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var ArmorSetBonusRank[]|Collection|Selectable
		 */
		private $ranks;

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
	}