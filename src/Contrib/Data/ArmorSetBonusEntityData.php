<?php
	namespace App\Contrib\Data;

	use App\Entity\ArmorSetBonus;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class ArmorBonusEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see ArmorSetBonus
	 */
	class ArmorSetBonusEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var ArmorSetBonusRankEntityData[]
		 */
		protected $ranks = [];

		/**
		 * ArmorBonusEntityData constructor.
		 *
		 * @param string $name
		 */
		protected function __construct(string $name) {
			$this->name = $name;
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
		 * @return ArmorSetBonusRankEntityData[]
		 */
		public function getRanks(): array {
			return $this->ranks;
		}

		/**
		 * @param ArmorSetBonusRankEntityData[] $ranks
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
			if (ObjectUtil::isset($data, 'name'))
				$this->setName($data->name);

			if (ObjectUtil::isset($data, 'ranks'))
				$this->setRanks(ArmorSetBonusRankEntityData::fromJsonArray($data->ranks));
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->name);
			$data->ranks = ArmorSetBonusRankEntityData::fromJsonArray($source->ranks);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof ArmorSetBonus))
				throw static::createLoadFailedException(ArmorSetBonus::class);

			$data = new static($entity->getName());
			$data->ranks = ArmorSetBonusRankEntityData::fromEntityCollection($entity->getRanks());

			return $data;
		}
	}