<?php
	namespace App\Game;

	final class BowCoatingType {
		const CLOSE_RANGE = 'close range';
		const POWER = 'power';
		const PARALYSIS = 'paralysis';
		const POISON = 'poison';
		const SLEEP = 'sleep';
		const BLAST = 'blast';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * CoatingType constructor.
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
		 * @param string $value
		 *
		 * @return bool
		 */
		public static function isValid(string $value): bool {
			return in_array($value, self::all());
		}
	}