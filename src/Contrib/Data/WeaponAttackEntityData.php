<?php
	namespace App\Contrib\Data;

	use App\Entity\WeaponAttackValues;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class WeaponAttackEntityData
	 *
	 * @package App\Contrib\Data
	 * @see     WeaponAttackValues
	 */
	class WeaponAttackEntityData extends AbstractEntityData {
		/**
		 * @var int
		 */
		protected $display;

		/**
		 * @var int
		 */
		protected $raw;

		/**
		 * WeaponAttackEntityData constructor.
		 *
		 * @param int $display
		 * @param int $raw
		 */
		protected function __construct(int $display, int $raw) {
			$this->display = $display;
			$this->raw = $raw;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'display'))
				$this->setDisplay($data->display);

			if (ObjectUtil::isset($data, 'raw'))
				$this->setRaw($data->raw);
		}

		/**
		 * @return int
		 */
		public function getDisplay(): int {
			return $this->display;
		}

		/**
		 * @param int $display
		 *
		 * @return $this
		 */
		public function setDisplay(int $display) {
			$this->display = $display;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getRaw(): int {
			return $this->raw;
		}

		/**
		 * @param int $raw
		 *
		 * @return $this
		 */
		public function setRaw(int $raw) {
			$this->raw = $raw;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'display' => $this->getDisplay(),
				'raw' => $this->getRaw(),
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			return new static($source->display, $source->raw);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof WeaponAttackValues))
				throw static::createLoadFailedException(WeaponAttackValues::class);

			return new static($entity->getDisplay(), $entity->getRaw());
		}
	}