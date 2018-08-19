<?php
	namespace App\Game;

	final class ArmorType {
		const HEAD = 'head';
		const CHEST = 'chest';
		const GLOVES = 'gloves';
		const WAIST = 'waist';
		const LEGS = 'legs';

		const ALL = [
			self::HEAD,
			self::CHEST,
			self::GLOVES,
			self::WAIST,
			self::LEGS,
		];

		/**
		 * ArmorType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $value
		 *
		 * @return bool
		 */
		public static function isValid(string $value): bool {
			return in_array($value, self::ALL);
		}
	}