<?php
	namespace App\Contrib\Data;

	use App\Entity\Camp;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class CampEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Camp
	 */
	class CampEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var int
		 */
		protected $zone;

		/**
		 * CampEntityData constructor.
		 *
		 * @param string $name
		 * @param int $zone
		 */
		protected function __construct(string $name, int $zone) {
			$this->name = $name;
			$this->zone = $zone;
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
		 * @return int
		 */
		public function getZone(): int {
			return $this->zone;
		}

		/**
		 * @param int $zone
		 *
		 * @return $this
		 */
		public function setZone(int $zone) {
			$this->zone = $zone;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'zone' => $this->getZone(),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'name'))
				$this->setName($data->name);

			if (ObjectUtil::isset($data, 'zone'))
				$this->setZone($data->zone);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			return new static($source->name, $source->zone);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof Camp))
				throw static::createLoadFailedException(Camp::class);

			return new static($entity->getName(), $entity->getZone());
		}
	}