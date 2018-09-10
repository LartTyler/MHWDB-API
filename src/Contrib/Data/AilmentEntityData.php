<?php
	namespace App\Contrib\Data;

	use App\Entity\Ailment;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

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
			$updater = new static();
			$updater->name = $source->name;
			$updater->description = $source->description;
			$updater->protection = AilmentProtectionEntityData::fromJson($source->protection);
			$updater->recovery = AilmentRecoveryEntityData::fromJson($source->recovery);

			return $updater;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Ailment))
				throw new \InvalidArgumentException(static::class . ' can only load ' . Ailment::class . ' entities');

			$updater = new static();
			$updater->name = $entity->getName();
			$updater->description = $entity->getDescription();
			$updater->protection = AilmentProtectionEntityData::fromEntity($entity->getProtection());
			$updater->recovery = AilmentRecoveryEntityData::fromEntity($entity->getRecovery());

			return $updater;
		}
	}