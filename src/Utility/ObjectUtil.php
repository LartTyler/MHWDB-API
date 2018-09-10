<?php
	namespace App\Utility;

	final class ObjectUtil {
		/**
		 * ObjectUtil constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param object $object
		 * @param string $property
		 *
		 * @return bool
		 */
		public static function isset(object $object, string $property): bool {
			return isset($object->{$property}) || property_exists($object, $property);
		}
	}