<?php
	namespace App\Contrib\Data;

	use App\Entity\Resistances;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ArmorResistancesEntityData extends AbstractEntityData {
		/**
		 * @var int
		 */
		protected $fire = 0;

		/**
		 * @var int
		 */
		protected $water = 0;

		/**
		 * @var int
		 */
		protected $ice = 0;

		/**
		 * @var int
		 */
		protected $thunder = 0;

		/**
		 * @var int
		 */
		protected $dragon = 0;

		/**
		 * @return int
		 */
		public function getFire(): int {
			return $this->fire;
		}

		/**
		 * @param int $fire
		 *
		 * @return $this
		 */
		public function setFire(int $fire) {
			$this->fire = $fire;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getWater(): int {
			return $this->water;
		}

		/**
		 * @param int $water
		 *
		 * @return $this
		 */
		public function setWater(int $water) {
			$this->water = $water;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getIce(): int {
			return $this->ice;
		}

		/**
		 * @param int $ice
		 *
		 * @return $this
		 */
		public function setIce(int $ice) {
			$this->ice = $ice;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getThunder(): int {
			return $this->thunder;
		}

		/**
		 * @param int $thunder
		 *
		 * @return $this
		 */
		public function setThunder(int $thunder) {
			$this->thunder = $thunder;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getDragon(): int {
			return $this->dragon;
		}

		/**
		 * @param int $dragon
		 *
		 * @return $this
		 */
		public function setDragon(int $dragon) {
			$this->dragon = $dragon;

			return $this;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'fire'))
				$this->setFire($data->fire);

			if (ObjectUtil::isset($data, 'water'))
				$this->setWater($data->water);

			if (ObjectUtil::isset($data, 'ice'))
				$this->setIce($data->ice);

			if (ObjectUtil::isset($data, 'thunder'))
				$this->setThunder($data->thunder);

			if (ObjectUtil::isset($data, 'dragon'))
				$this->setDragon($data->dragon);
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'fire' => $this->getFire(),
				'water' => $this->getWater(),
				'ice' => $this->getIce(),
				'thunder' => $this->getThunder(),
				'dragon' => $this->getDragon(),
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static();
			$data
				->setFire($source->fire)
				->setWater($source->water)
				->setIce($source->ice)
				->setThunder($source->thunder)
				->setDragon($source->dragon);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return AbstractEntityData|ArmorResistancesEntityData
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Resistances))
				throw static::createLoadFailedException(Resistances::class);

			$data = new static();
			$data
				->setFire($entity->getFire())
				->setWater($entity->getWater())
				->setIce($entity->getIce())
				->setThunder($entity->getThunder())
				->setDragon($entity->getDragon());

			return $data;
		}
	}