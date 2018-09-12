<?php
	namespace App\Contrib\Data;

	use App\Entity\Decoration;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class DecorationEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Decoration
	 */
	class DecorationEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $slug;

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var int
		 */
		protected $slot;

		/**
		 * @var int
		 */
		protected $rarity;

		/**
		 * @var SimpleSkillRankEntityData[]
		 */
		protected $skills = [];

		/**
		 * DecorationEntityData constructor.
		 *
		 * @param string $name
		 * @param string $slug
		 * @param int    $slot
		 * @param int    $rarity
		 */
		protected function __construct(string $name, string $slug, int $slot, int $rarity) {
			$this->name = $name;
			$this->slug = $slug;
			$this->slot = $slot;
			$this->rarity = $rarity;
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
		 * @return int
		 */
		public function getSlot(): int {
			return $this->slot;
		}

		/**
		 * @param int $slot
		 *
		 * @return $this
		 */
		public function setSlot(int $slot) {
			$this->slot = $slot;

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
		 * @return SimpleSkillRankEntityData[]
		 */
		public function getSkills(): array {
			return $this->skills;
		}

		/**
		 * @param SimpleSkillRankEntityData[] $skills
		 *
		 * @return $this
		 */
		public function setSkills(array $skills) {
			$this->skills = $skills;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'slug' => $this->getSlug(),
				'slot' => $this->getSlot(),
				'rarity' => $this->getRarity(),
				'skills' => static::normalizeArray($this->getSkills()),
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

			if (ObjectUtil::isset($data, 'slot'))
				$this->setSlot($data->slot);

			if (ObjectUtil::isset($data, 'rarity'))
				$this->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'skills'))
				$this->setSkills(SimpleSkillRankEntityData::fromJsonArray($data->skills));
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->name, $source->slug, $source->slot, $source->rarity);
			$data->skills = SimpleSkillRankEntityData::fromJsonArray($source->skills);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Decoration))
				throw static::createLoadFailedException(Decoration::class);

			$data = new static($entity->getName(), $entity->getSlug(), $entity->getSlot(), $entity->getRarity());
			$data->skills = SimpleSkillRankEntityData::fromEntityCollection($entity->getSkills());

			return $data;
		}
	}