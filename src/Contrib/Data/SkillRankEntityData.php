<?php
	namespace App\Contrib\Data;

	use App\Entity\SkillRank;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class SkillRankEntityData
	 *
	 * @package App\Contrib\Data
	 * @see SkillRank
	 */
	class SkillRankEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $slug;

		/**
		 * @var int
		 */
		protected $level;

		/**
		 * @var string
		 */
		protected $description;

		/**
		 * @var object
		 */
		protected $modifiers;

		/**
		 * SkillRankEntityData constructor.
		 *
		 * @param string $slug
		 * @param int    $level
		 * @param string $description
		 */
		protected function __construct(string $slug, int $level, string $description) {
			$this->slug = $slug;
			$this->level = $level;
			$this->description = $description;
			$this->modifiers = new \stdClass();
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
		 * @return object
		 */
		public function getModifiers(): object {
			return $this->modifiers;
		}

		/**
		 * @param object $modifiers
		 *
		 * @return $this
		 */
		public function setModifiers(object $modifiers) {
			$this->modifiers = $modifiers;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'slug' => $this->getSlug(),
				'level' => $this->getLevel(),
				'description' => $this->getDescription(),
				'modifiers' => $this->getModifiers(),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'slug'))
				$this->setSlug($data->slug);

			if (ObjectUtil::isset($data, 'level'))
				$this->setLevel($data->level);

			if (ObjectUtil::isset($data, 'description'))
				$this->setDescription($data->description);

			if (ObjectUtil::isset($data, 'modifiers'))
				$this->setModifiers($data->modifiers);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static($source->slug, $source->level, $source->description);
			$data->modifiers = $source->modifiers;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof SkillRank))
				throw static::createLoadFailedException(SkillRank::class);

			$data = new static($entity->getSlug(), $entity->getLevel(), $entity->getDescription());
			$data->modifiers = json_decode(json_encode((object)$entity->getModifiers()));

			return $data;
		}
	}