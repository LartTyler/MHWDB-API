<?php
	namespace App\Contrib\Data;

	use App\Entity\Weapon;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class WeaponEntityData
	 *
	 * @package App\Contrib\Data
	 * @see     Weapon
	 */
	class WeaponEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var string
		 */
		protected $slug;

		/**
		 * @var string
		 */
		protected $type;

		/**
		 * @var int
		 */
		protected $rarity;

		/**
		 * @var WeaponAttackEntityData
		 */
		protected $attack;

		/**
		 * @var \stdClass
		 */
		protected $attributes;

		/**
		 * @var SlotEntityData[]
		 */
		protected $slots = [];

		/**
		 * @var WeaponSharpnessEntityData[]
		 */
		protected $durability = [];

		/**
		 * @var WeaponElementEntityData[]
		 */
		protected $elements = [];

		/**
		 * @var WeaponCraftingInfoEntityData|null
		 */
		protected $crafting = null;

		/**
		 * @var WeaponAssetsEntityData|null
		 */
		protected $assets = null;

		/**
		 * WeaponEntityData constructor.
		 *
		 * @param string $name
		 * @param string $slug
		 * @param string $type
		 * @param int    $rarity
		 */
		protected function __construct(string $name, string $slug, string $type, int $rarity) {
			$this->name = $name;
			$this->slug = $slug;
			$this->type = $type;
			$this->rarity = $rarity;
			$this->attributes = new \stdClass();
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
		 * @return int
		 */
		public function getRarity(): int {
			return $this->rarity;
		}

		/**
		 * @param int $rarity
		 *
		 * @return $this
		 */
		public function setRarity(int $rarity) {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return WeaponAttackEntityData
		 */
		public function getAttack(): WeaponAttackEntityData {
			return $this->attack;
		}

		/**
		 * @return \stdClass
		 */
		public function getAttributes(): \stdClass {
			return $this->attributes;
		}

		/**
		 * @param \stdClass $attributes
		 *
		 * @return $this
		 */
		public function setAttributes(\stdClass $attributes) {
			$this->attributes = $attributes;

			return $this;
		}

		/**
		 * @return SlotEntityData[]
		 */
		public function getSlots(): array {
			return $this->slots;
		}

		/**
		 * @param SlotEntityData[] $slots
		 *
		 * @return $this
		 */
		public function setSlots(array $slots) {
			$this->slots = $slots;

			return $this;
		}

		/**
		 * @return WeaponSharpnessEntityData[]
		 */
		public function getDurability(): array {
			return $this->durability;
		}

		/**
		 * @param WeaponSharpnessEntityData[] $durability
		 *
		 * @return $this
		 */
		public function setDurability(array $durability) {
			$this->durability = $durability;

			return $this;
		}

		/**
		 * @return WeaponElementEntityData[]
		 */
		public function getElements(): array {
			return $this->elements;
		}

		/**
		 * @param WeaponElementEntityData[] $elements
		 *
		 * @return $this
		 */
		public function setElements(array $elements) {
			$this->elements = $elements;

			return $this;
		}

		/**
		 * @return WeaponCraftingInfoEntityData|null
		 */
		public function getCrafting(): ?WeaponCraftingInfoEntityData {
			return $this->crafting;
		}

		/**
		 * @param WeaponCraftingInfoEntityData|null $crafting
		 *
		 * @return $this
		 */
		public function setCrafting(?WeaponCraftingInfoEntityData $crafting) {
			$this->crafting = $crafting;

			return $this;
		}

		/**
		 * @return WeaponAssetsEntityData|null
		 */
		public function getAssets(): ?WeaponAssetsEntityData {
			return $this->assets;
		}

		/**
		 * @param WeaponAssetsEntityData|null $assets
		 *
		 * @return $this
		 */
		public function setAssets(?WeaponAssetsEntityData $assets) {
			$this->assets = $assets;

			return $this;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'name'))
				$this->setName($data->name);

			if (ObjectUtil::isset($data, 'slug'))
				$this->setSlug($data->slug);

			if (ObjectUtil::isset($data, 'type'))
				$this->setType($data->type);

			if (ObjectUtil::isset($data, 'rarity'))
				$this->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'attack'))
				$this->getAttack()->update($data->attack);

			if (ObjectUtil::isset($data, 'attributes'))
				$this->setAttributes($data->attributes);

			if (ObjectUtil::isset($data, 'slots'))
				$this->setSlots(SlotEntityData::fromJsonArray($data->slots));

			if (ObjectUtil::isset($data, 'durability'))
				$this->setDurability(WeaponSharpnessEntityData::fromJsonArray($data->durability));

			if (ObjectUtil::isset($data, 'elements'))
				$this->setElements(WeaponElementEntityData::fromJsonArray($data->elements));

			if (ObjectUtil::isset($data, 'crafting')) {
				$value = $data->crafting;

				if ($value && $this->getCrafting())
					$this->getCrafting()->update($value);
				else if ($value)
					$this->setCrafting(WeaponCraftingInfoEntityData::fromJson($value));
				else
					$this->setCrafting(null);
			}

			if (ObjectUtil::isset($data, 'assets')) {
				$value = $data->assets;

				if ($value && $this->getAssets())
					$this->getAssets()->update($value);
				else if ($value)
					$this->setAssets(WeaponAssetsEntityData::fromJson($value));
				else
					$this->setAssets(null);
			}
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'slug' => $this->getSlug(),
				'type' => $this->getType(),
				'rarity' => $this->getRarity(),
				'attack' => $this->getAttack()->normalize(),
				'attributes' => $this->getAttributes(),
				'slots' => static::normalizeArray($this->getSlots()),
				'durability' => static::normalizeArray($this->getDurability()),
				'elements' => static::normalizeArray($this->getElements()),
				'crafting' => $this->getCrafting() ? $this->getCrafting()->normalize() : null,
				'assets' => $this->getAssets() ? $this->getAssets()->normalize() : null,
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->name, $source->slug, $source->type, $source->rarity);
			$data->attack = WeaponAttackEntityData::fromJson($source->attack);
			$data->attributes = $source->attributes;
			$data->slots = SlotEntityData::fromJsonArray($source->slots);
			$data->durability = WeaponSharpnessEntityData::fromJsonArray($source->durability);
			$data->elements = WeaponElementEntityData::fromJsonArray($source->elements);

			if ($source->crafting)
				$data->crafting = WeaponCraftingInfoEntityData::fromJson($source->crafting);

			if ($source->assets)
				$data->assets = WeaponAssetsEntityData::fromJson($source->assets);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Weapon))
				throw static::createLoadFailedException(Weapon::class);

			$data = new static($entity->getName(), $entity->getSlug(), $entity->getType(), $entity->getRarity());
			$data->attack = WeaponAttackEntityData::fromEntity($entity->getAttack());
			$data->attributes = json_decode(json_encode((object)$entity->getAttributes()));
			$data->slots = SlotEntityData::fromEntityCollection($entity->getSlots());
			$data->durability = WeaponSharpnessEntityData::fromEntityCollection($entity->getDurability());
			$data->elements = WeaponElementEntityData::fromEntityCollection($entity->getElements());

			if ($crafting = $entity->getCrafting())
				$data->crafting = WeaponCraftingInfoEntityData::fromEntity($crafting);

			if ($assets = $entity->getAssets())
				$data->assets = WeaponAssetsEntityData::fromEntity($assets);

			return $data;
		}
	}