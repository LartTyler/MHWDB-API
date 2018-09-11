<?php
	namespace App\Contrib\Data;

	use App\Entity\Slot;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class SlotEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Slot
	 */
	class SlotEntityData extends AbstractEntityData {
		/**
		 * @var int
		 */
		protected $rank;

		/**
		 * SlotEntityData constructor.
		 *
		 * @param int $rank
		 */
		protected function __construct(int $rank) {
			$this->rank = $rank;
		}

		/**
		 * @return int
		 */
		public function getRank(): int {
			return $this->rank;
		}

		/**
		 * @param int $rank
		 *
		 * @return $this
		 */
		public function setRank(int $rank) {
			$this->rank = $rank;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'rank' => $this->getRank(),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'rank'))
				$this->setRank($data->rank);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			return new static($source->rank);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Slot))
				throw static::createLoadFailedException(Slot::class);

			return new static($entity->getRank());
		}
	}