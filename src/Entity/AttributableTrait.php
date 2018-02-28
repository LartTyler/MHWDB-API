<?php
	namespace App\Entity;

	trait AttributableTrait {
		/**
		 * @var array
		 */
		protected $attributes = [];

		/**
		 * @return array
		 */
		public function getAttributes(): array {
			return $this->attributes;
		}

		/**
		 * @param string $attribute
		 * @param mixed  $def
		 *
		 * @return string|int|bool|null
		 */
		public function getAttribute(string $attribute, $def = null) {
			if (isset($this->attributes[$attribute]))
				return $this->attributes[$attribute];

			return $def;
		}

		/**
		 * @param array $attributes
		 *
		 * @return $this
		 */
		public function setAttributes(array $attributes) {
			$this->attributes = [];

			foreach ($attributes as $attribute => $value)
				$this->setAttribute($attribute, $value);

			return $this;
		}

		/**
		 * @param string               $attribute
		 * @param string|int|bool|null $value
		 *
		 * @return $this
		 */
		public function setAttribute(string $attribute, $value) {
			if ($value === null)
				return $this->removeAttribute($attribute);

			$this->attributes[$attribute] = $value;

			return $this;
		}

		/**
		 * @param string $attribute
		 *
		 * @return $this
		 */
		public function removeAttribute(string $attribute) {
			unset($this->attributes[$attribute]);

			return $this;
		}
	}