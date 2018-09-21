<?php
	namespace App\Contrib\Data;
	
	use App\Entity\ArmorDefenseValues;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ArmorDefenseEntityData extends AbstractEntityData {
		/**
		 * @var int
		 */
		protected $base;

		/**
		 * @var int
		 */
		protected $max;

		/**
		 * @var int
		 */
		protected $augmented;

		/**
		 * ArmorDefenseEntityData constructor.
		 *
		 * @param int $base
		 * @param int $max
		 * @param int $augmented
		 */
		protected function __construct(int $base, int $max, int $augmented) {
			$this->base = $base;
			$this->max = $max;
			$this->augmented = $augmented;
		}

		/**
		 * @return int
		 */
		public function getBase(): int {
			return $this->base;
		}

		/**
		 * @param int $base
		 *
		 * @return $this
		 */
		public function setBase(int $base) {
			$this->base = $base;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getMax(): int {
			return $this->max;
		}

		/**
		 * @param int $max
		 *
		 * @return $this
		 */
		public function setMax(int $max) {
			$this->max = $max;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getAugmented(): int {
			return $this->augmented;
		}

		/**
		 * @param int $augmented
		 *
		 * @return $this
		 */
		public function setAugmented(int $augmented) {
			$this->augmented = $augmented;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'base' => $this->getBase(),
				'max' => $this->getMax(),
				'augmented' => $this->getAugmented(),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'base'))
				$this->setBase($data->base);

			if (ObjectUtil::isset($data, 'max'))
				$this->setMax($data->max);

			if (ObjectUtil::isset($data, 'augmented'))
				$this->setAugmented($data->augmented);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			return new static($source->base, $source->max, $source->augmented);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof ArmorDefenseValues))
				throw static::createLoadFailedException(ArmorDefenseValues::class);

			return new static($entity->getBase(), $entity->getMax(), $entity->getAugmented());
		}
	}