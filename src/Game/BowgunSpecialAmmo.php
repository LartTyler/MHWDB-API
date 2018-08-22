<?php
	namespace App\Game;

	final class BowgunSpecialAmmo {
		const WYVERNBLAST = 'wyvernblast';
		const WYVERNHEART = 'wyvernheart';
		const WYVERNSNIPE = 'wyvernsnipe';

		const ALL = [
			self::WYVERNBLAST,
			self::WYVERNHEART,
			self::WYVERNSNIPE,
		];

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
			return in_array($value, self::ALL);
		}
	}