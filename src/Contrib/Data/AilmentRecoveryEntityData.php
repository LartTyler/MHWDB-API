<?php
	namespace App\Contrib\Data;

	use App\Entity\AilmentRecovery;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class AilmentRecoveryEntityData extends AbstractEntityData {
		/**
		 * @var string[]
		 */
		protected $actions;

		/**
		 * @var int[]
		 */
		protected $items;

		/**
		 * @return string[]
		 */
		public function getActions(): array {
			return $this->actions;
		}

		/**
		 * @param string[] $actions
		 *
		 * @return $this
		 */
		public function setActions(array $actions) {
			$this->actions = $actions;

			return $this;
		}

		/**
		 * @return int[]
		 */
		public function getItems(): array {
			return $this->items;
		}

		/**
		 * @param int[] $items
		 *
		 * @return $this
		 */
		public function setItems(array $items) {
			$this->items = $items;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'actions' => $this->actions,
				'items' => $this->items,
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'actions'))
				$this->actions = $data->actions;

			if (ObjectUtil::isset($data, 'items'))
				$this->items = $data->items;
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$updater = new static();
			$updater->actions = $source->actions;
			$updater->items = $source->items;

			return $updater;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof AilmentRecovery)) {
				throw new \InvalidArgumentException(static::class . ' can only load ' . AilmentRecovery::class .
					' entities');
			}

			$updater = new static();
			$updater->actions = $entity->getActions();
			$updater->items = static::toIdArray($entity->getItems());

			return $updater;
		}
	}