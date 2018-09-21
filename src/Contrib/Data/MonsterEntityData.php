<?php
	namespace App\Contrib\Data;

	use App\Entity\Monster;
	use App\Game\Element;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class MonsterEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Monster
	 */
	class MonsterEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var string
		 */
		protected $type;

		/**
		 * @var string
		 */
		protected $species;

		/**
		 * @var int[]
		 */
		protected $ailments = [];

		/**
		 * @var int[]
		 */
		protected $locations = [];

		/**
		 * @var MonsterResistanceEntityData[]
		 */
		protected $resistances = [];

		/**
		 * @var MonsterWeaknessEntityData[]
		 */
		protected $weaknesses = [];

		/**
		 * @var string|null
		 */
		protected $description = null;

		/**
		 * @var string[]
		 * @see Element::DAMAGE
		 */
		protected $elements = [];

		/**
		 * MonsterEntityData constructor.
		 *
		 * @param string $name
		 * @param string $type
		 * @param string $species
		 */
		protected function __construct(string $name, string $type, string $species) {
			$this->name = $name;
			$this->type = $type;
			$this->species = $species;
		}

		/**
		 * @return string
		 */
		public function doGetEntityGroupName(): ?string {
			return 'monsters/' . $this->getType();
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
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @param string $type
		 *
		 * @return $this
		 */
		public function setType(string $type) {
			$this->type = $type;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getSpecies(): string {
			return $this->species;
		}

		/**
		 * @param string $species
		 *
		 * @return $this
		 */
		public function setSpecies(string $species) {
			$this->species = $species;

			return $this;
		}

		/**
		 * @return int[]
		 */
		public function getAilments(): array {
			return $this->ailments;
		}

		/**
		 * @param int[] $ailments
		 *
		 * @return $this
		 */
		public function setAilments(array $ailments) {
			$this->ailments = $ailments;

			return $this;
		}

		/**
		 * @return int[]
		 */
		public function getLocations(): array {
			return $this->locations;
		}

		/**
		 * @param int[] $locations
		 *
		 * @return $this
		 */
		public function setLocations(array $locations) {
			$this->locations = $locations;

			return $this;
		}

		/**
		 * @return MonsterResistanceEntityData[]
		 */
		public function getResistances(): array {
			return $this->resistances;
		}

		/**
		 * @param MonsterResistanceEntityData[] $resistances
		 *
		 * @return $this
		 */
		public function setResistances(array $resistances) {
			$this->resistances = $resistances;

			return $this;
		}

		/**
		 * @return MonsterWeaknessEntityData[]
		 */
		public function getWeaknesses(): array {
			return $this->weaknesses;
		}

		/**
		 * @param MonsterWeaknessEntityData[] $weaknesses
		 *
		 * @return $this
		 */
		public function setWeaknesses(array $weaknesses) {
			$this->weaknesses = $weaknesses;

			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getDescription(): ?string {
			return $this->description;
		}

		/**
		 * @param null|string $description
		 *
		 * @return $this
		 */
		public function setDescription(?string $description) {
			$this->description = $description;

			return $this;
		}

		/**
		 * @return string[]
		 */
		public function getElements(): array {
			return $this->elements;
		}

		/**
		 * @param string[] $elements
		 *
		 * @return $this
		 */
		public function setElements(array $elements) {
			$this->elements = $elements;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'type' => $this->getType(),
				'species' => $this->getSpecies(),
				'ailments' => $this->getAilments(),
				'locations' => $this->getLocations(),
				'resistances' => static::normalizeArray($this->getResistances()),
				'weaknesses' => static::normalizeArray($this->getWeaknesses()),
				'description' => $this->getDescription(),
				'elements' => $this->getElements(),
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

			if (ObjectUtil::isset($data, 'type'))
				$this->setType($data->type);

			if (ObjectUtil::isset($data, 'species'))
				$this->setSpecies($data->species);

			if (ObjectUtil::isset($data, 'ailments'))
				$this->setAilments($data->ailments);

			if (ObjectUtil::isset($data, 'locations'))
				$this->setLocations($data->locations);

			if (ObjectUtil::isset($data, 'resistances'))
				$this->setResistances(MonsterResistanceEntityData::fromJsonArray($data->resistances));

			if (ObjectUtil::isset($data, 'weaknesses'))
				$this->setWeaknesses(MonsterWeaknessEntityData::fromJsonArray($data->weaknesses));

			if (ObjectUtil::isset($data, 'description'))
				$this->setDescription($data->description);

			if (ObjectUtil::isset($data, 'elements'))
				$this->setElements($data->elements);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static($source->name, $source->type, $source->species);
			$data->ailments = $source->ailments;
			$data->locations = $source->locations;
			$data->resistances = MonsterResistanceEntityData::fromJsonArray($source->resistances);
			$data->weaknesses = MonsterWeaknessEntityData::fromJsonArray($source->weaknesses);
			$data->description = $source->description;
			$data->elements = $source->elements;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof Monster))
				throw static::createLoadFailedException(Monster::class);

			$data = new static($entity->getName(), $entity->getType(), $entity->getSpecies());
			$data->ailments = static::toIdArray($entity->getAilments());
			$data->locations = static::toIdArray($entity->getLocations());
			$data->resistances = MonsterResistanceEntityData::fromEntityCollection($entity->getResistances());
			$data->weaknesses = MonsterWeaknessEntityData::fromEntityCollection($entity->getWeaknesses());
			$data->description = $entity->getDescription();
			$data->elements = $entity->getElements();

			return $data;
		}
	}