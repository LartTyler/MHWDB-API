<?php
	namespace App\Contrib\Data;

	use App\Entity\Charm;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class CharmEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Charm
	 */
	class CharmEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $slug;

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var CharmRankEntityData[]
		 */
		protected $ranks = [];

		/**
		 * CharmEntityData constructor.
		 *
		 * @param string $name
		 * @param string $slug
		 */
		protected function __construct(string $name, string $slug) {
			$this->name = $name;
			$this->slug = $slug;
		}

		/**
		 * @return string
		 */
		public function getSlug(): string {
			return $this->slug;
		}

		/**
		 * @param string $slug
		 *
		 * @return $this
		 */
		public function setSlug(string $slug) {
			$this->slug = $slug;

			return $this;
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
		 * @return CharmRankEntityData[]
		 */
		public function getRanks(): array {
			return $this->ranks;
		}

		/**
		 * @param CharmRankEntityData[] $ranks
		 *
		 * @return $this
		 */
		public function setRanks(array $ranks) {
			$this->ranks = $ranks;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'slug' => $this->getSlug(),
				'name' => $this->getName(),
				'ranks' => static::normalizeArray($this->getRanks()),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'slug'))
				$this->setSlug($data->slug);

			if (ObjectUtil::isset($data, 'name'))
				$this->setName($data->name);

			if (ObjectUtil::isset($data, 'ranks'))
				$this->setRanks(CharmRankEntityData::fromJsonArray($data->ranks));
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->name, $source->slug);
			$data->ranks = CharmRankEntityData::fromJsonArray($source->ranks);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Charm))
				throw static::createLoadFailedException(Charm::class);

			$data = new static($entity->getName(), $entity->getSlug());
			$data->ranks = CharmRankEntityData::fromEntityCollection($entity->getRanks());

			return $data;
		}
	}