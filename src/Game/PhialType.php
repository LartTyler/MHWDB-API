<?php
	namespace App\Game;

	final class PhialType {
		public const ELEMENT = 'element';
		public const IMPACT = 'impact';
		public const POWER = 'power';
		public const POWER_ELEMENT = 'power element';
		public const DRAGON = 'dragon';
		public const EXHAUST = 'exhaust';
		public const PARA = 'para';
		public const POISON = 'poison';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * PhialType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $type
		 *
		 * @return bool
		 */
		public static function isDamageRequired(string $type): bool {
			return in_array(
				$type,
				[
					self::DRAGON,
					self::EXHAUST,
					self::PARA,
					self::POISON,
				]
			);
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$types === null)
				self::$types = array_values((new \ReflectionClass(self::class))->getConstants());

			return self::$types;
		}
	}