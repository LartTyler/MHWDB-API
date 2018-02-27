<?php
	namespace App\Entity;

	use App\Game\Attribute;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class ItemAttribute implements EntityInterface {
		use EntityTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var mixed
		 */
		private $value;

		/**
		 * Attribute constructor.
		 *
		 * @param string $name One of the {@see Attribute} class constants.
		 * @param mixed  $value
		 *
		 * @see Attribute
		 */
		public function __construct(string $name, $value) {
			$this->name = $name;
			$this->value = $value;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return mixed
		 */
		public function getValue() {
			return $this->value;
		}

		/**
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public function setValue($value): ItemAttribute {
			$this->value = $value;

			return $this;
		}
	}