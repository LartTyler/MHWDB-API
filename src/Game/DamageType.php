<?php
	namespace App\Game;

	final class DamageType {
		const BLUNT = 'blunt';
		const SEVER = 'sever';
		const PROJECTILE = 'projectile';

		/**
		 * @var string[]
		 */
		private static $allValues = null;

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$allValues === null)
				self::$allValues = (new \ReflectionClass(self::class))->getConstants();

			return self::$allValues;
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
		 * DamageType constructor.
		 */
		private function __construct() {
		}
	}