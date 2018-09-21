<?php
	namespace App\Contrib\Data;

	use App\Entity\AilmentProtection;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class AilmentProtectionEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see AilmentProtection
	 */
	class AilmentProtectionEntityData extends AbstractEntityData {
		/**
		 * @var string[]
		 */
		protected $items;

		/**
		 * @var string[]
		 */
		protected $skills;

		/**
		 * @return string[]
		 */
		public function getItems(): array {
			return $this->items;
		}

		/**
		 * @param string[] $items
		 *
		 * @return $this
		 */
		public function setItems(array $items) {
			$this->items = $items;

			return $this;
		}

		/**
		 * @return string[]
		 */
		public function getSkills(): array {
			return $this->skills;
		}

		/**
		 * @param string[] $skills
		 *
		 * @return $this
		 */
		public function setSkills(array $skills) {
			$this->skills = $skills;

			return $this;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'skills'))
				$this->skills = $data->skills;

			if (ObjectUtil::isset($data, 'items'))
				$this->items = $data->items;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'items' => $this->items,
				'skills' => $this->skills,
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$updater = new static();
			$updater->items = $source->items;
			$updater->skills = $source->skills;

			return $updater;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof AilmentProtection))
				throw static::createLoadFailedException(AilmentProtection::class);

			$updater = new static();
			$updater->items = static::toIdArray($entity->getItems());
			$updater->skills = static::toIdArray($entity->getSkills());

			return $updater;
		}
	}