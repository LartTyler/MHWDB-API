<?php
	namespace App\Game;

	final class Elderseal {
		const LOW = 'low';
		const AVERAGE = 'average';
		const HIGH = 'high';

		const ALL = [
			self::LOW,
			self::AVERAGE,
			self::HIGH,
		];

		/**
		 * @param string $value
		 *
		 * @return bool
		 */
		public static function isValid(string $value): bool {
			return in_array($value, self::ALL);
		}

		/**
		 * Elderseal constructor.
		 */
		private function __construct() {
		}
	}