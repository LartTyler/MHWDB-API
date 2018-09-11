<?php
	namespace App\Contrib\Data;

	use App\Entity\CharmRank;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class CharmRankEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see CharmRank
	 */
	class CharmRankEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var int
		 */
		protected $level;

		/**
		 * @var SkillRankEntityData[]
		 */
		protected $skills;

		/**
		 * @var int
		 */
		protected $rarity = 0;

		/**
		 * @var CharmRankCraftingInfoEntityData|null
		 */
		protected $crafting = null;

		/**
		 * CharmRankEntityData constructor.
		 *
		 * @param string $name
		 * @param int    $level
		 */
		protected function __construct(string $name, int $level) {
			$this->name = $name;
			$this->level = $level;
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
		public function getLevel(): int {
			return $this->level;
		}

		/**
		 * @param int $level
		 *
		 * @return $this
		 */
		public function setLevel(int $level) {
			$this->level = $level;

			return $this;
		}

		/**
		 * @return SkillRankEntityData[]
		 */
		public function getSkills(): array {
			return $this->skills;
		}

		/**
		 * @param SkillRankEntityData[] $skills
		 *
		 * @return $this
		 */
		public function setSkills(array $skills) {
			$this->skills = $skills;

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
		 * @return CharmRankCraftingInfoEntityData|null
		 */
		public function getCrafting(): ?CharmRankCraftingInfoEntityData {
			return $this->crafting;
		}

		/**
		 * @param CharmRankCraftingInfoEntityData|null $crafting
		 *
		 * @return $this
		 */
		public function setCrafting(?CharmRankCraftingInfoEntityData $crafting) {
			$this->crafting = $crafting;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'level' => $this->getLevel(),
				'skills' => static::normalizeArray($this->getSkills()),
				'rarity' => $this->getRarity(),
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

			if (ObjectUtil::isset($data, 'level'))
				$this->setLevel($data->level);

			if (ObjectUtil::isset($data, 'skills'))
				$this->setSkills(SkillRankEntityData::fromJsonArray($data->skills));

			if (ObjectUtil::isset($data, 'rarity'))
				$this->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'crafting')) {
				$value = $data->crafting;

				if ($value && $this->getCrafting())
					$this->getCrafting()->update($value);
				else if ($value)
					$this->setCrafting(CharmRankCraftingInfoEntityData::fromJson($value));
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
			$data = new static($source->name, $source->level);
			$data->rarity = $source->rarity;
			$data->skills = SkillRankEntityData::fromJsonArray($source->skills);

			if ($source->crafting)
				$data->crafting = CharmRankCraftingInfoEntityData::fromJson($source->crafting);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof CharmRank))
				throw static::createLoadFailedException(CharmRank::class);

			$data = new static($entity->getName(), $entity->getLevel());
			$data->rarity = $entity->getRarity();
			$data->skills = SkillRankEntityData::fromEntityCollection($entity->getSkills());

			if ($crafting = $entity->getCrafting())
				$data->crafting = CharmRankCraftingInfoEntityData::fromEntity($crafting);

			return $data;
		}
	}