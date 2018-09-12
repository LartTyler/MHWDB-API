<?php
	namespace App\Contrib\Data;

	use App\Entity\WeaponCraftingInfo;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class WeaponCraftingInfoEntityData
	 *
	 * @package App\Contrib\Data
	 * @see     WeaponCraftingInfo
	 */
	class WeaponCraftingInfoEntityData extends AbstractEntityData {
		/**
		 * @var bool
		 */
		protected $craftable;

		/**
		 * @var int|null
		 */
		protected $previous = null;

		/**
		 * @var int[]
		 */
		protected $branches = [];

		/**
		 * @var CraftingMaterialCostEntityData[]
		 */
		protected $craftingMaterials = [];

		/**
		 * @var CraftingMaterialCostEntityData[]
		 */
		protected $upgradeMaterials = [];

		/**
		 * WeaponCraftingInfoEntityData constructor.
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
		 * @return int|null
		 */
		public function getPrevious(): ?int {
			return $this->previous;
		}

		/**
		 * @param int|null $previous
		 *
		 * @return $this
		 */
		public function setPrevious(?int $previous) {
			$this->previous = $previous;

			return $this;
		}

		/**
		 * @return int[]
		 */
		public function getBranches(): array {
			return $this->branches;
		}

		/**
		 * @param int[] $branches
		 *
		 * @return $this
		 */
		public function setBranches(array $branches) {
			$this->branches = $branches;

			return $this;
		}

		/**
		 * @return CraftingMaterialCostEntityData[]
		 */
		public function getCraftingMaterials(): array {
			return $this->craftingMaterials;
		}

		/**
		 * @param CraftingMaterialCostEntityData[] $craftingMaterials
		 *
		 * @return $this
		 */
		public function setCraftingMaterials(array $craftingMaterials) {
			$this->craftingMaterials = $craftingMaterials;

			return $this;
		}

		/**
		 * @return CraftingMaterialCostEntityData[]
		 */
		public function getUpgradeMaterials(): array {
			return $this->upgradeMaterials;
		}

		/**
		 * @param CraftingMaterialCostEntityData[] $upgradeMaterials
		 *
		 * @return $this
		 */
		public function setUpgradeMaterials(array $upgradeMaterials) {
			$this->upgradeMaterials = $upgradeMaterials;

			return $this;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'craftable'))
				$this->setCraftable($data->craftable);

			if (ObjectUtil::isset($data, 'previous'))
				$this->setPrevious($data->previous);

			if (ObjectUtil::isset($data, 'branches'))
				$this->setBranches($data->branches);

			if (ObjectUtil::isset($data, 'craftingMaterials'))
				$this->setCraftingMaterials(CraftingMaterialCostEntityData::fromJsonArray($data->craftingMaterials));

			if (ObjectUtil::isset($data, 'upgradeMaterials'))
				$this->setUpgradeMaterials(CraftingMaterialCostEntityData::fromJsonArray($data->upgradeMaterials));
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'craftable' => $this->isCraftable(),
				'previous' => $this->getPrevious(),
				'branches' => $this->getBranches(),
				'craftingMaterials' => static::normalizeArray($this->getCraftingMaterials()),
				'upgradeMaterials' => static::normalizeArray($this->getUpgradeMaterials()),
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->craftable);
			$data->previous = $source->previous;
			$data->branches = $source->branches;
			$data->craftingMaterials = CraftingMaterialCostEntityData::fromJsonArray($source->craftingMaterials);
			$data->upgradeMaterials = CraftingMaterialCostEntityData::fromJsonArray($source->upgradeMaterials);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof WeaponCraftingInfo))
				throw static::createLoadFailedException(WeaponCraftingInfo::class);

			$data = new static($entity->isCraftable());
			$data->previous = $entity->getPrevious()->getId();
			$data->branches = static::toIdArray($entity->getBranches());
			$data->craftingMaterials =
				CraftingMaterialCostEntityData::fromEntityCollection($entity->getCraftingMaterials());
			$data->upgradeMaterials =
				CraftingMaterialCostEntityData::fromEntityCollection($entity->getUpgradeMaterials());

			return $data;
		}
	}