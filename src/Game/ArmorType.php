<?php
	namespace App\Game;

	final class ArmorType {
		const HEAD = 'head';
		const CHEST = 'chest';
		const GLOVES = 'gloves';
		const WAIST = 'waist';
		const LEGS = 'legs';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * ArmorType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$types === null)
				self::$types = (new \ReflectionClass(self::class))->getConstants();

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