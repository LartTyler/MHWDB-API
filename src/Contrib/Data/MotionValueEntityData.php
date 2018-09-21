<?php
	namespace App\Contrib\Data;

	use App\Entity\MotionValue;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class MotionValueEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see MotionValue
	 */
	class MotionValueEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var string
		 */
		protected $weaponType;

		/**
		 * @var string|null
		 */
		protected $damageType = null;

		/**
		 * @var int|null
		 */
		protected $stun = null;

		/**
		 * @var int|null
		 */
		protected $exhaust = null;

		/**
		 * @var int[]
		 */
		protected $hits = [];

		/**
		 * MotionValueEntityData constructor.
		 *
		 * @param string $name
		 * @param string $weaponType
		 */
		protected function __construct(string $name, string $weaponType) {
			$this->name = $name;
			$this->weaponType = $weaponType;
		}

		/**
		 * @return string
		 */
		public function doGetEntityGroupName(): ?string {
			return 'motion-values/' . $this->getWeaponType();
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
		public function getWeaponType(): string {
			return $this->weaponType;
		}

		/**
		 * @param string $weaponType
		 *
		 * @return $this
		 */
		public function setWeaponType(string $weaponType) {
			$this->weaponType = $weaponType;

			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getDamageType(): ?string {
			return $this->damageType;
		}

		/**
		 * @param null|string $damageType
		 *
		 * @return $this
		 */
		public function setDamageType(?string $damageType) {
			$this->damageType = $damageType;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getStun(): ?int {
			return $this->stun;
		}

		/**
		 * @param int|null $stun
		 *
		 * @return $this
		 */
		public function setStun(?int $stun) {
			$this->stun = $stun;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getExhaust(): ?int {
			return $this->exhaust;
		}

		/**
		 * @param int|null $exhaust
		 *
		 * @return $this
		 */
		public function setExhaust(?int $exhaust) {
			$this->exhaust = $exhaust;

			return $this;
		}

		/**
		 * @return int[]
		 */
		public function getHits(): array {
			return $this->hits;
		}

		/**
		 * @param int[] $hits
		 *
		 * @return $this
		 */
		public function setHits(array $hits) {
			$this->hits = $hits;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'weaponType' => $this->getWeaponType(),
				'damageType' => $this->getDamageType(),
				'stun' => $this->getStun(),
				'exhaust' => $this->getExhaust(),
				'hits' => $this->getHits(),
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

			if (ObjectUtil::isset($data, 'weaponType'))
				$this->setWeaponType($data->weaponType);

			if (ObjectUtil::isset($data, 'damageType'))
				$this->setDamageType($data->damageType);

			if (ObjectUtil::isset($data, 'stun'))
				$this->setStun($data->stun);

			if (ObjectUtil::isset($data, 'exhaust'))
				$this->setExhaust($data->exhaust);

			if (ObjectUtil::isset($data, 'hits'))
				$this->setHits($data->hits);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static($source->name, $source->weaponType);
			$data->damageType = $source->damageType;
			$data->stun = $source->stun;
			$data->exhaust = $source->exhaust;
			$data->hits = $source->hits;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof MotionValue))
				throw static::createLoadFailedException(MotionValue::class);

			$data = new static($entity->getName(), $entity->getWeaponType());
			$data->damageType = $entity->getDamageType();
			$data->stun = $entity->getStun();
			$data->exhaust = $entity->getExhaust();
			$data->hits = $entity->getHits();

			return $data;
		}
	}