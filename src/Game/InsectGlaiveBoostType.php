<?php
	namespace App\Game;

	final class InsectGlaiveBoostType {
		const SEVER = 'sever';
		const BLUNT = 'blunt';
		const ELEMENT = 'element';
		const SPEED = 'speed';
		const HEALTH = 'health';
		const STAMINA = 'stamina';

		/**
		 * @var string[]|null
		 */
		protected static $types = null;

		/**
		 * InsectGlaiveBoostType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$types === null)
				self::$types = array_values((new \ReflectionClass(self::class))->getConstants());

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