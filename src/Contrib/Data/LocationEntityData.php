<?php
	namespace App\Contrib\Data;

	use App\Entity\Location;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class LocationEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Location
	 */
	class LocationEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var int
		 */
		protected $zoneCount;

		/**
		 * @var CampEntityData[]
		 */
		protected $camps = [];

		/**
		 * LocationEntityData constructor.
		 *
		 * @param string $name
		 * @param int    $zoneCount
		 */
		protected function __construct(string $name, int $zoneCount) {
			$this->name = $name;
			$this->zoneCount = $zoneCount;
		}

		/**
		 * @return string
		 */
		public function doGetEntityGroupName(): ?string {
			return 'locations';
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
		public function getZoneCount(): int {
			return $this->zoneCount;
		}

		/**
		 * @param int $zoneCount
		 *
		 * @return $this
		 */
		public function setZoneCount(int $zoneCount) {
			$this->zoneCount = $zoneCount;

			return $this;
		}

		/**
		 * @return CampEntityData[]
		 */
		public function getCamps(): array {
			return $this->camps;
		}

		/**
		 * @param CampEntityData[] $camps
		 *
		 * @return $this
		 */
		public function setCamps(array $camps) {
			$this->camps = $camps;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'zoneCount' => $this->getZoneCount(),
				'camps' => static::normalizeArray($this->getCamps()),
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

			if (ObjectUtil::isset($data, 'zoneCount'))
				$this->setZoneCount($data->zoneCount);

			if (ObjectUtil::isset($data, 'camps'))
				$this->setCamps(CampEntityData::fromJsonArray($data->camps));
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static($source->name, $source->zoneCount);
			$data->camps = CampEntityData::fromJsonArray($source->camps);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof Location))
				throw static::createLoadFailedException(Location::class);

			$data = new static($entity->getName(), $entity->getZoneCount());
			$data->camps = CampEntityData::fromEntityCollection($entity->getCamps());

			return $data;
		}
	}