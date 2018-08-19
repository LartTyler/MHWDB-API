<?php
	namespace App\Game;

	final class BowgunDeviation {
		const NONE = 'none';
		const LOW = 'low';
		const AVERAGE = 'average';
		const HIGH = 'high';

		const ALL = [
			self::NONE,
			self::LOW,
			self::AVERAGE,
			self::HIGH,
		];

		/**
		 * Deviation constructor.
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