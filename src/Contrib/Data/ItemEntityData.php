<?php
	namespace App\Contrib\Data;

	use App\Entity\Item;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class ItemEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see Item
	 */
	class ItemEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var string
		 */
		protected $description;

		/**
		 * @var int
		 */
		protected $rarity;

		/**
		 * @var int
		 */
		protected $value = 0;

		/**
		 * @var int
		 */
		protected $carryLimit = 0;

		/**
		 * ItemEntityData constructor.
		 *
		 * @param string $name
		 * @param string $description
		 * @param int    $rarity
		 */
		protected function __construct(string $name, string $description, int $rarity) {
			$this->name = $name;
			$this->description = $description;
			$this->rarity = $rarity;
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
		 * @return int
		 */
		public function getRarity(): int {
			return $this->rarity;
		}

		/**
		 * @param int $rarity
		 *
		 * @return $this
		 */
		public function setRarity(int $rarity) {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getValue(): int {
			return $this->value;
		}

		/**
		 * @param int $value
		 *
		 * @return $this
		 */
		public function setValue(int $value) {
			$this->value = $value;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getCarryLimit(): int {
			return $this->carryLimit;
		}

		/**
		 * @param int $carryLimit
		 *
		 * @return $this
		 */
		public function setCarryLimit(int $carryLimit) {
			$this->carryLimit = $carryLimit;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'name' => $this->getName(),
				'description' => $this->getDescription(),
				'rarity' => $this->getRarity(),
				'value' => $this->getValue(),
				'carryLimit' => $this->getCarryLimit(),
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

			if (ObjectUtil::isset($data, 'description'))
				$this->setDescription($data->description);

			if (ObjectUtil::isset($data, 'rarity'))
				$this->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'value'))
				$this->setValue($data->value);

			if (ObjectUtil::isset($data, 'carryLimit'))
				$this->setCarryLimit($data->carryLimit);
		}

		/**
		 * @param object $source
		 *
		 * @return AbstractEntityData|ItemEntityData
		 */
		public static function fromJson(object $source) {
			$data = new static($source->name, $source->description, $source->rarity);
			$data->value = $source->value;
			$data->carryLimit = $source->carryLimit;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return AbstractEntityData|ItemEntityData
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof Item))
				throw static::createLoadFailedException(Item::class);

			$data = new static($entity->getName(), $entity->getDescription(), $entity->getRarity());
			$data->value = $entity->getValue();
			$data->carryLimit = $entity->getCarryLimit();

			return $data;
		}
	}