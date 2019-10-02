<?php
	namespace App\Game;

	final class ShellingType {
		public const NORMAL = 'normal';
		public const LONG = 'long';
		public const WIDE = 'wide';

		/**
		 * @var string[]|null
		 */
		private static $values = null;

		/**
		 * ShellingType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return string[]
		 */
		public static function all() {
			if (self::$values === null)
				self::$values = array_values((new \ReflectionClass(self::class))->getConstants());

			return self::$values;
		}
	}