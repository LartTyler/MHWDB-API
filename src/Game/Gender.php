<?php
	namespace App\Game;

	final class Gender {
		const MALE = 'male';
		const FEMALE = 'female';

		const ALL = [
			self::MALE,
			self::FEMALE,
		];

		/**
		 * Gender constructor.
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