<?php
	namespace App\Contrib\Data;

	use App\Entity\ArmorCraftingInfo;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class ArmorCraftingInfoEntityData
	 *
	 * @package App\Contrib\Data\
	 *
	 * @see ArmorCraftingInfo
	 */
	class ArmorCraftingInfoEntityData extends AbstractEntityData {
		/**
		 * @var CraftingMaterialCostEntityData[]
		 */
		protected $materials = [];

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
				'materials' => static::normalizeArray($this->getMaterials()),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'materials'))
				$this->setMaterials(CraftingMaterialCostEntityData::fromJsonArray($data->materials));
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static();
			$data->materials = CraftingMaterialCostEntityData::fromJsonArray($source->materials);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof ArmorCraftingInfo))
				throw static::createLoadFailedException(ArmorCraftingInfo::class);

			$data = new static();
			$data->materials = CraftingMaterialCostEntityData::fromEntityCollection($entity->getMaterials());

			return $data;
		}
	}