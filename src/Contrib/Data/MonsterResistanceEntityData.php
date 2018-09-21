<?php
	namespace App\Contrib\Data;

	use App\Entity\MonsterResistance;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class MonsterResistanceEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see MonsterResistance
	 */
	class MonsterResistanceEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $element;

		/**
		 * @var string|null
		 */
		protected $condition = null;

		/**
		 * MonsterResistanceEntityData constructor.
		 *
		 * @param string $element
		 */
		protected function __construct(string $element) {
			$this->element = $element;
		}

		/**
		 * @return string
		 */
		public function getElement(): string {
			return $this->element;
		}

		/**
		 * @param string $element
		 *
		 * @return $this
		 */
		public function setElement(string $element) {
			$this->element = $element;

			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getCondition(): ?string {
			return $this->condition;
		}

		/**
		 * @param null|string $condition
		 *
		 * @return $this
		 */
		public function setCondition(?string $condition) {
			$this->condition = $condition;

			return $this;
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'element' => $this->getElement(),
				'condition' => $this->getCondition(),
			];
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'element'))
				$this->setElement($data->element);

			if (ObjectUtil::isset($data, 'condition'))
				$this->setCondition($data->condition);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static($source->element);
			$data->condition = $source->condition;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof MonsterResistance))
				throw static::createLoadFailedException(MonsterResistance::class);

			$data = new static($entity->getElement());
			$data->condition = $entity->getCondition();

			return $data;
		}
	}