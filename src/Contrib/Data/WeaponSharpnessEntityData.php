<?php
	namespace App\Contrib\Data;

	use App\Entity\WeaponSharpness;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * Class WeaponSharpnessEntityData
	 *
	 * @package App\Contrib\Data
	 * @see     WeaponSharpness
	 */
	class WeaponSharpnessEntityData extends AbstractEntityData {
		/**
		 * @var int
		 */
		protected $red = 0;

		/**
		 * @var int
		 */
		protected $orange = 0;

		/**
		 * @var int
		 */
		protected $yellow = 0;

		/**
		 * @var int
		 */
		protected $green = 0;

		/**
		 * @var int
		 */
		protected $blue = 0;

		/**
		 * @var int
		 */
		protected $white = 0;

		/**
		 * @return int
		 */
		public function getRed(): int {
			return $this->red;
		}

		/**
		 * @param int $red
		 *
		 * @return $this
		 */
		public function setRed(int $red) {
			$this->red = $red;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getOrange(): int {
			return $this->orange;
		}

		/**
		 * @param int $orange
		 *
		 * @return $this
		 */
		public function setOrange(int $orange) {
			$this->orange = $orange;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getYellow(): int {
			return $this->yellow;
		}

		/**
		 * @param int $yellow
		 *
		 * @return $this
		 */
		public function setYellow(int $yellow) {
			$this->yellow = $yellow;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getGreen(): int {
			return $this->green;
		}

		/**
		 * @param int $green
		 *
		 * @return $this
		 */
		public function setGreen(int $green) {
			$this->green = $green;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getBlue(): int {
			return $this->blue;
		}

		/**
		 * @param int $blue
		 *
		 * @return $this
		 */
		public function setBlue(int $blue) {
			$this->blue = $blue;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getWhite(): int {
			return $this->white;
		}

		/**
		 * @param int $white
		 *
		 * @return $this
		 */
		public function setWhite(int $white) {
			$this->white = $white;

			return $this;
		}

		/**
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void {
			if (ObjectUtil::isset($data, 'red'))
				$this->setRed($data->red);

			if (ObjectUtil::isset($data, 'orange'))
				$this->setOrange($data->orange);

			if (ObjectUtil::isset($data, 'yellow'))
				$this->setYellow($data->yellow);

			if (ObjectUtil::isset($data, 'green'))
				$this->setGreen($data->green);

			if (ObjectUtil::isset($data, 'blue'))
				$this->setBlue($data->blue);

			if (ObjectUtil::isset($data, 'white'))
				$this->setWhite($data->white);
		}

		/**
		 * @return array
		 */
		protected function doNormalize(): array {
			return [
				'red' => $this->getRed(),
				'orange' => $this->getOrange(),
				'yellow' => $this->getYellow(),
				'green' => $this->getGreen(),
				'blue' => $this->getBlue(),
				'white' => $this->getWhite(),
			];
		}

		/**
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source) {
			$data = new static();
			$data->red = $source->red;
			$data->orange = $source->orange;
			$data->yellow = $source->yellow;
			$data->green = $source->green;
			$data->blue = $source->blue;
			$data->white = $source->white;

			return $data;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity) {
			if (!($entity instanceof WeaponSharpness))
				throw static::createLoadFailedException(WeaponSharpness::class);

			$data = new static();
			$data->red = $entity->getRed();
			$data->orange = $entity->getOrange();
			$data->yellow = $entity->getYellow();
			$data->green = $entity->getGreen();
			$data->blue = $entity->getBlue();
			$data->white = $entity->getWhite();

			return $data;
		}
	}