<?php
	namespace App\Game;

	final class BowgunSpecialAmmo {
		const WYVERNBLAST = 'wyvernblast';
		const WYVERNHEART = 'wyvernheart';
		const WYVERNSNIPE = 'wyvernsnipe';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * SpecialAmmo constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $value
		 *
		 * @return bool
		 */
		public static function isValid(string $value): bool {
			return in_array($value, self::all());
		}

		/**
		 * @return string[]
		 */
		public static function all() {
			if (self::$types === null)
				self::$types = array_values((new \ReflectionClass(self::class))->getConstants());

			return self::$types;
		}
	}