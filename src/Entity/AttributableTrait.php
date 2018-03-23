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
		 * @return mixed|null
		 */
		public function getAttribute(string $attribute, $def = null) {
			return $this->attributes[$attribute] ?? $def;
		}

		/**
		 * @param array $attributes
		 *
		 * @return $this
		 */
		public function setAttributes(array $attributes) {
			$this->attributes = [];

			return $this->addAttributes($attributes);
		}

		/**
		 * @param array $attributes
		 *
		 * @return $this
		 */
		public function addAttributes(array $attributes) {
			foreach ($attributes as $key => $value)
				$this->setAttribute($key, $value);

			return $this;
		}

		/**
		 * @param string     $attribute
		 * @param mixed|null $value
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