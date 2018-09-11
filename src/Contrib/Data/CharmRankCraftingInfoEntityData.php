<?php
	namespace App\Contrib\Data;

	use App\Entity\CharmRankCraftingInfo;
	use App\Entity\CraftingMaterialCost;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class CharmRankCraftingInfoEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see CharmRankCraftingInfo
	 */
	class CharmRankCraftingInfoEntityData extends AbstractEntityData {
		/**
		 * @var bool
		 */
		protected $craftable;

		/**
		 * @var CraftingMaterialCostEntityData[]
		 */
		protected $materials = [];

		/**
		 * CharmRankCraftingInfoEntityData constructor.
		 *
		 * @param bool $craftable
		 */
		protected function __construct(bool $craftable) {
			$this->craftable = $craftable;
		}

		/**
		 * @return bool
		 */
		public function isCraftable(): bool {
			return $this->craftable;
		}

		/**
		 * @param bool $craftable
		 *
		 * @return $this
		 */
		public function setCraftable(bool $craftable) {
			$this->craftable = $craftable;

			return $this;
		}

		/**
		 * @return CraftingMaterialCostEntityData[]
		 */
		public function getMaterials(): array {
			return $this->materials;
		}

		/**
		 * @param CraftingMaterialCostEntityData[] $materials
		 *
		 * @return $this
		 */
		public function setMaterials(array $materials) {
			$this->materials = $materials;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'craftable' => $this->isCraftable(),
				'materials' => static::normalizeArray($this->getMaterials()),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'craftable'))
				$this->setCraftable($data->craftable);

			if (ObjectUtil::isset($data, 'materials'))
				$this->setMaterials(CraftingMaterialCostEntityData::fromJsonArray($data->materials));
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->craftable);
			$data->materials = CraftingMaterialCostEntityData::fromJsonArray($source->materials);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof CharmRankCraftingInfo))
				throw static::createLoadFailedException(CharmRankCraftingInfo::class);

			$data = new static($entity->isCraftable());
			$data->materials = CraftingMaterialCostEntityData::fromEntityCollection($entity->getMaterials());

			return $data;
		}
	}