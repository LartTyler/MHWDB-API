<?php
	namespace App\Contrib\Data;

	use App\Entity\Skill;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class SkillEntityData
	 *
	 * @package App\Contrib\Data
	 * @see Skill
	 */
	class SkillEntityData extends AbstractEntityData {
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
		protected $description;

		/**
		 * @var SkillRankEntityData[]
		 */
		protected $ranks = [];

		/**
		 * SkillEntityData constructor.
		 *
		 * @param string $name
		 * @param string $slug
		 * @param string $description
		 */
		protected function __construct(string $name, string $slug, string $description) {
			$this->name = $name;
			$this->slug = $slug;
			$this->description = $description;
		}

		/**
		 * @return string
		 */
		public function doGetEntityGroupName(): ?string {
			return 'skills';
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
		public function getDescription(): string {
			return $this->description;
		}

		/**
		 * @param string $description
		 *
		 * @return $this
		 */
		public function setDescription(string $description) {
			$this->description = $description;

			return $this;
		}

		/**
		 * @return SkillRankEntityData[]
		 */
		public function getRanks(): array {
			return $this->ranks;
		}

		/**
		 * @param SkillRankEntityData[] $ranks
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
				'slug' => $this->getSlug(),
				'description' => $this->getDescription(),
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

			if (ObjectUtil::isset($data, 'slug'))
				$this->setSlug($data->slug);

			if (ObjectUtil::isset($data, 'description'))
				$this->setDescription($data->description);

			if (ObjectUtil::isset($data, 'ranks'))
				$this->setRanks(SkillRankEntityData::fromJsonArray($data->ranks));
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->name, $source->slug, $source->description);
			$data->ranks = SkillRankEntityData::fromJsonArray($source->ranks);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Skill))
				throw static::createLoadFailedException(Skill::class);

			$data = new static($entity->getName(), $entity->getSlug(), $entity->getDescription());
			$data->ranks = SkillRankEntityData::fromEntityCollection($entity->getRanks());

			return $data;
		}
	}