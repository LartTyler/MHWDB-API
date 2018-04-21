<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class Slot implements EntityInterface {
		use EntityTrait;

		/**
		 * @var int
		 */
		private $rank;

		/**
		 * Slot constructor.
		 *
		 * @param int $rank
		 */
		public function __construct(int $rank) {
			$this->rank = $rank;
		}

		/**
		 * @return int
		 */
		public function getRank(): int {
			return $this->rank;
		}
	}