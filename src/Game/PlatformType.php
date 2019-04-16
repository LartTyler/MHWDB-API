<?php
	namespace App\Game;

	final class PlatformType {
		public const CONSOLE = 'console';
		public const PC = 'pc';

		/**
		 * @var string[]
		 */
		private static $types = null;

		/**
		 * PlatformType constructor.
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