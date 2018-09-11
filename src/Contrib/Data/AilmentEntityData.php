<?php
	namespace App\Contrib\Data;

	use App\Entity\Ailment;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class AilmentEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Ailment
	 */
	class AilmentEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var string
		 */
		protected $description;

		/**
		 * @var AilmentProtectionEntityData
		 */
		protected $protection;

		/**
		 * @var AilmentRecoveryEntityData
		 */
		protected $recovery;

		/**
		 * AilmentEntityData constructor.
		 *
		 * @param string $name
		 * @param string $description
		 */
		protected function __construct(string $name, string $description) {
			$this->name = $name;
			$this->description = $description;
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
		 * @return AilmentProtectionEntityData
		 */
		public function getProtection(): AilmentProtectionEntityData {
			return $this->protection;
		}

		/**
		 * @return AilmentRecoveryEntityData
		 */
		public function getRecovery(): AilmentRecoveryEntityData {
			return $this->recovery;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if ($name = $data->name ?? null)
				$this->setName($name);

			if ($description = $data->description ?? null)
				$this->setDescription($description);

			if ($protection = $data->protection ?? null)
				$this->getProtection()->update($protection);

			if ($recovery = $data->recovery ?? null)
				$this->getRecovery()->update($recovery);
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'description' => $this->getDescription(),
				'protection' => $this->protection->normalize(),
				'recovery' => $this->recovery->normalize(),
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static($source->name, $source->description);
			$data->protection = AilmentProtectionEntityData::fromJson($source->protection);
			$data->recovery = AilmentRecoveryEntityData::fromJson($source->recovery);

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Ailment))
				throw static::createLoadFailedException(Ailment::class);

			$data = new static($entity->getName(), $entity->getDescription());
			$data->protection = AilmentProtectionEntityData::fromEntity($entity->getProtection());
			$data->recovery = AilmentRecoveryEntityData::fromEntity($entity->getRecovery());

			return $data;
		}
	}