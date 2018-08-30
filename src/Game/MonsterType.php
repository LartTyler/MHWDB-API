<?php
	namespace App\Game;

	final class MonsterType {
		public const SMALL = 'small';
		public const LARGE = 'large';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * MonsterType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$types === null)
				self::$types = array_values((new \ReflectionClass(self::class))->getConstants());

			return self::$types;
		}

		/**
		 * @param string $string
		 *
		 * @return bool
		 */
		public static function isValid(string $string): bool {
			return in_array($string, self::all());
		}
	}