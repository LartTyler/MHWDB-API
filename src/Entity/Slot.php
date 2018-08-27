<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="slots")
	 *
	 * Class Slot
	 *
	 * @package App\Entity
	 */
	class Slot implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
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