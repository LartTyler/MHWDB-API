<?php
	namespace App\Game;

	final class PlatformExclusivityType {
		public const PS4 = 'ps4';

		public const XBOX = 'xbox';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * PlatformExclusivityType constructor.
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
	}