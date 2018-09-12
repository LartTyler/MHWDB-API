<?php
	namespace App\Contrib\Data;

	use App\Entity\WeaponElement;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class WeaponElementEntityData
	 *
	 * @package App\Contrib\Data
	 * @see     WeaponElement
	 */
	class WeaponElementEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $type;

		/**
		 * @var int
		 */
		protected $damage;

		/**
		 * @var bool
		 */
		protected $hidden;

		/**
		 * WeaponElementEntityData constructor.
		 *
		 * @param string $type
		 * @param int    $damage
		 * @param bool   $hidden
		 */
		protected function __construct(string $type, int $damage, bool $hidden) {
			$this->type = $type;
			$this->damage = $damage;
			$this->hidden = $hidden;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'type'))
				$this->setType($data->type);

			if (ObjectUtil::isset($data, 'damage'))
				$this->setDamage($data->damage);

			if (ObjectUtil::isset($data, 'hidden'))
				$this->setHidden($data->hidden);
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
		public function getDamage(): int {
			return $this->damage;
		}

		/**
		 * @param int $damage
		 *
		 * @return $this
		 */
		public function setDamage(int $damage) {
			$this->damage = $damage;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function isHidden(): bool {
			return $this->hidden;
		}

		/**
		 * @param bool $hidden
		 *
		 * @return $this
		 */
		public function setHidden(bool $hidden) {
			$this->hidden = $hidden;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'type' => $this->getType(),
				'damage' => $this->getDamage(),
				'hidden' => $this->isHidden(),
			];
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof WeaponElement))
				throw static::createLoadFailedException(WeaponElement::class);

			return new static($entity->getType(), $entity->getDamage(), $entity->isHidden());
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			return new static($source->type, $source->damage, $source->hidden);
		}
	}