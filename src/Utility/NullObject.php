<?php
	namespace App\Utility;

	class NullObject {
		/**
		 * @param mixed $name
		 * @param mixed $arguments
		 *
		 * @return void
		 */
		public function __call($name, $arguments) {
			return null;
		}

		/**
		 * @param object|null $value
		 *
		 * @return object|NullObject
		 */
		public static function of(?object $value) {
			return $value ?? new static();
		}
	}