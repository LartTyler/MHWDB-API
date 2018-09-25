<?php
	namespace App\Contrib\Data;

	use App\Entity\ArmorSet;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class ArmorSetEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see ArmorSet
	 */
	class ArmorSetEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var string
		 */
		protected $rank;

		/**
		 * @var int[]
		 */
		protected $pieces = [];

		/**
		 * @var int|null
		 */
		protected $bonus = null;

		/**
		 * ArmorSetEntityData constructor.
		 *
		 * @param string $name
		 * @param string $rank
		 */
		protected function __construct(string $name, string $rank) {
			$this->name = $name;
			$this->rank = $rank;
		}

		/**
		 * @return string
		 */
		public function doGetEntityGroupName(): ?string {
			return 'armor-sets/' . $this->getRank();
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
		public function getRank(): string {
			return $this->rank;
		}

		/**
		 * @param string $rank
		 *
		 * @return $this
		 */
		public function setRank(string $rank) {
			$this->rank = $rank;

			return $this;
		}

		/**
		 * @return int[]
		 */
		public function getPieces(): array {
			return $this->pieces;
		}

		/**
		 * @param int[] $pieces
		 *
		 * @return $this
		 */
		public function setPieces(array $pieces) {
			$this->pieces = $pieces;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getBonus(): ?int {
			return $this->bonus;
		}

		/**
		 * @param int|null $bonus
		 *
		 * @return $this
		 */
		public function setBonus(?int $bonus) {
			$this->bonus = $bonus;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'rank' => $this->getRank(),
				'pieces' => $this->getPieces(),
				'bonus' => $this->getBonus(),
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

			if (ObjectUtil::isset($data, 'rank'))
				$this->setRank($data->rank);

			if (ObjectUtil::isset($data, 'pieces'))
				$this->setPieces($data->pieces);

			if (ObjectUtil::isset($data, 'bonus'))
				$this->setBonus($data->bonus);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static($source->name, $source->rank);
			$data->pieces = $source->pieces;

			if ($source->bonus)
				$data->bonus = $source->bonus;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof ArmorSet))
				throw static::createLoadFailedException(ArmorSet::class);

			$data = new static($entity->getName(), $entity->getRank());
			$data->pieces = static::toIdArray($entity->getPieces());

			if ($bonus = $entity->getBonus())
				$data->bonus = $entity->getBonus()->getId();

			return $data;
		}
	}