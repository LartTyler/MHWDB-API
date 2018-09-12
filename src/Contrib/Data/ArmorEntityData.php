<?php
	namespace App\Contrib\Data;

	use App\Entity\Armor;
	use App\Entity\AttributableTrait;
	use App\Entity\SluggableTrait;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class ArmorEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Armor
	 */
	class ArmorEntityData extends AbstractEntityData {
		use SluggableTrait;
		use AttributableTrait;

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
		protected $rank;

		/**
		 * @var int
		 */
		protected $rarity;

		/**
		 * @var ArmorResistancesEntityData
		 */
		protected $resistances;

		/**
		 * @var ArmorDefenseEntityData
		 */
		protected $defense;

		/**
		 * @var int[]
		 */
		protected $skills;

		/**
		 * @var SlotEntityData[]
		 */
		protected $slots;

		/**
		 * @var int|null
		 */
		protected $armorSet = null;

		/**
		 * @var ArmorAssetsEntityData|null
		 */
		protected $assets = null;

		/**
		 * @var ArmorCraftingInfoEntityData|null
		 */
		protected $crafting = null;

		/**
		 * ArmorEntityData constructor.
		 *
		 * @param string $name
		 * @param string $type
		 * @param string $rank
		 * @param int    $rarity
		 */
		protected function __construct(string $name, string $type, string $rank, int $rarity) {
			$this->name = $name;
			$this->type = $type;
			$this->rank = $rank;
			$this->rarity = $rarity;
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
		 * @return ArmorResistancesEntityData
		 */
		public function getResistances(): ArmorResistancesEntityData {
			return $this->resistances;
		}

		/**
		 * @return ArmorDefenseEntityData
		 */
		public function getDefense(): ArmorDefenseEntityData {
			return $this->defense;
		}

		/**
		 * @return int[]
		 */
		public function getSkills(): array {
			return $this->skills;
		}

		/**
		 * @param int[] $skills
		 *
		 * @return $this
		 */
		public function setSkills(array $skills) {
			$this->skills = $skills;

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
		 * @return int|null
		 */
		public function getArmorSet(): ?int {
			return $this->armorSet;
		}

		/**
		 * @param int|null $armorSet
		 *
		 * @return $this
		 */
		public function setArmorSet(?int $armorSet) {
			$this->armorSet = $armorSet;

			return $this;
		}

		/**
		 * @return ArmorAssetsEntityData|null
		 */
		public function getAssets(): ?ArmorAssetsEntityData {
			return $this->assets;
		}

		/**
		 * @param ArmorAssetsEntityData|null $assets
		 *
		 * @return $this
		 */
		public function setAssets(?ArmorAssetsEntityData $assets) {
			$this->assets = $assets;

			return $this;
		}

		/**
		 * @return ArmorCraftingInfoEntityData|null
		 */
		public function getCrafting(): ?ArmorCraftingInfoEntityData {
			return $this->crafting;
		}

		/**
		 * @param ArmorCraftingInfoEntityData|null $crafting
		 *
		 * @return $this
		 */
		public function setCrafting(?ArmorCraftingInfoEntityData $crafting) {
			$this->crafting = $crafting;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'slug' => $this->getSlug(),
				'attributes' => $this->getAttributes() ?: new \stdClass(),
				'name' => $this->getName(),
				'type' => $this->getType(),
				'rank' => $this->getRank(),
				'rarity' => $this->getRarity(),
				'resistances' => $this->getResistances()->normalize(),
				'defense' => $this->getDefense()->normalize(),
				'skills' => $this->getSkills(),
				'slots' => static::normalizeArray($this->getSlots()),
				'armorSet' => $this->getArmorSet(),
				'assets' => $this->getAssets() ? $this->getAssets()->normalize() : null,
				'crafting' => $this->getCrafting() ? $this->getCrafting()->normalize() : null,
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

			if (ObjectUtil::isset($data, 'slug'))
				$this->setSlug($data->slug);

			if (ObjectUtil::isset($data, 'attributes'))
				$this->setAttributes($data->attributes);

			if (ObjectUtil::isset($data, 'type'))
				$this->setType($data->type);

			if (ObjectUtil::isset($data, 'rank'))
				$this->setRank($data->rank);

			if (ObjectUtil::isset($data, 'resistances'))
				$this->getResistances()->update($data->resistances);

			if (ObjectUtil::isset($data, 'defense'))
				$this->getDefense()->update($data->defense);

			if (ObjectUtil::isset($data, 'skills'))
				$this->setSkills($data->skills);

			if (ObjectUtil::isset($data, 'slots'))
				$this->setSlots(SlotEntityData::fromJsonArray($data->slots));

			if (ObjectUtil::isset($data, 'armorSet'))
				$this->setArmorSet($data->armorSet);

			if (ObjectUtil::isset($data, 'assets')) {
				$value = $data->assets;

				if ($value && $this->getAssets())
					$this->getAssets()->update($value);
				else if ($value)
					$this->setAssets(ArmorAssetsEntityData::fromJson($value));
				else
					$this->setAssets(null);
			}

			if (ObjectUtil::isset($data, 'crafting')) {
				$value = $data->crafting;

				if ($value && $this->getCrafting())
					$this->getCrafting()->update($value);
				else if ($value)
					$this->setCrafting(ArmorCraftingInfoEntityData::fromJson($value));
				else
					$this->setCrafting(null);
			}
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->name, $source->type, $source->rank, $source->rarity);
			$data->resistances = ArmorResistancesEntityData::fromJson($source->resistances);
			$data->defense = ArmorDefenseEntityData::fromJson($source->defense);
			$data->armorSet = $source->armorSet;
			$data->skills = SimpleSkillRankEntityData::fromJsonArray($source->skills);
			$data->slots = SlotEntityData::fromJsonArray($source->slots);

			if ($source->assets)
				$data->assets = ArmorAssetsEntityData::fromJson($source->assets);

			if ($source->crafting)
				$data->crafting = ArmorCraftingInfoEntityData::fromJson($source->crafting);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Armor))
				throw static::createLoadFailedException(Armor::class);

			$data = new static($entity->getName(), $entity->getType(), $entity->getRank(), $entity->getRarity());
			$data->resistances = ArmorResistancesEntityData::fromEntity($entity->getResistances());
			$data->defense = ArmorDefenseEntityData::fromEntity($entity->getDefense());
			$data->skills = SimpleSkillRankEntityData::fromEntityCollection($entity->getSkills());
			$data->slots = SlotEntityData::fromEntityCollection($entity->getSlots());

			if ($armorSet = $entity->getArmorSet())
				$data->armorSet = $armorSet->getId();

			if ($assets = $entity->getAssets())
				$data->assets = ArmorAssetsEntityData::fromEntity($assets);

			if ($crafting = $entity->getCrafting())
				$data->crafting = ArmorCraftingInfoEntityData::fromEntity($crafting);

			return $data;
		}
	}