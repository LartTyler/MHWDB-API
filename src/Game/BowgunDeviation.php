<?php
	namespace App\Game;

	final class BowgunDeviation {
		const NONE = 'none';
		const LOW = 'low';
		const AVERAGE = 'average';
		const HIGH = 'high';

		/**
		 * @var string[]|null
		 */
		private static $values = null;

		/**
		 * Deviation constructor.
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

		/**
		 * @param string $value
		 *
		 * @return bool
		 */
		public static function isValid(string $value): bool {
			return in_array($value, self::all());
		}
	}