<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @package App\Entity
	 */
	abstract class Slot implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		protected $rank;

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