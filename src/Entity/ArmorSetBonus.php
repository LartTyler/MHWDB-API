<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
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
	}