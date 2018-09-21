<?php
	namespace App\Contrib\Data;

	use App\Entity\MonsterWeakness;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class MonsterWeaknessEntityData
	 *
	 * @package App\Contrib\Data
	 *
	 * @see MonsterWeakness
	 */
	class MonsterWeaknessEntityData extends AbstractEntityData {
		/**
		 * @var string
		 */
		protected $element;

		/**
		 * @var int
		 */
		protected $stars;

		/**
		 * @var string|null
		 */
		protected $condition = null;

		/**
		 * MonsterWeaknessEntityData constructor.
		 *
		 * @param string $element
		 * @param int    $stars
		 */
		protected function __construct(string $element, int $stars) {
			$this->element = $element;
			$this->stars = $stars;
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
		 * @return int
		 */
		public function getStars(): int {
			return $this->stars;
		}

		/**
		 * @param int $stars
		 *
		 * @return $this
		 */
		public function setStars(int $stars) {
			$this->stars = $stars;

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
				'stars' => $this->getStars(),
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

			if (ObjectUtil::isset($data, 'stars'))
				$this->setStars($data->stars);

			if (ObjectUtil::isset($data, 'condition'))
				$this->setCondition($data->condition);
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function doFromJson(object $source) {
			$data = new static($source->element, $source->stars);
			$data->condition = $source->condition;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function doFromEntity(EntityInterface $entity) {
			if (!($entity instanceof MonsterWeakness))
				throw static::createLoadFailedException(MonsterWeakness::class);

			$data = new static($entity->getElement(), $entity->getStars());
			$data->condition = $entity->getCondition();

			return $data;
		}
	}