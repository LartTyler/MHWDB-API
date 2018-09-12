<?php
	namespace App\Contrib\Data;

	use App\Entity\SkillRank;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class SkillRankEntityData
	 *
	 * @package App\Contrib\Data
	 * @see     SkillRank
	 */
	class SimpleSkillRankEntityData extends AbstractEntityData {
		/**
		 * @var int
		 */
		protected $skill;

		/**
		 * @var int
		 */
		protected $level;

		/**
		 * ArmorSkillEntityData constructor.
		 *
		 * @param int $skill
		 * @param int $level
		 */
		protected function __construct(int $skill, int $level) {
			$this->skill = $skill;
			$this->level = $level;
		}

		/**
		 * @return int
		 */
		public function getSkill(): int {
			return $this->skill;
		}

		/**
		 * @param int $skill
		 *
		 * @return $this
		 */
		public function setSkill(int $skill) {
			$this->skill = $skill;

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
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'skill'))
				$this->setSkill($data->skill);

			if (ObjectUtil::isset($data, 'level'))
				$this->setLevel($data->level);
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'skill' => $this->getSkill(),
				'level' => $this->getLevel(),
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			return new static($source->skill, $source->level);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof SkillRank))
				throw static::createLoadFailedException(SkillRank::class);

			return new static($entity->getSkill()->getId(), $entity->getLevel());
		}
	}