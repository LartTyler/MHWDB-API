<?php
	namespace App\Game;

	final class Rank {
		const LOW = 'low';
		const HIGH = 'high';

		/**
		 * @var string[]|null
		 */
		private static $ranks = null;

		/**
		 * ArmorRank constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return array
		 */
		public static function all(): array {
			if (self::$ranks === null)
				self::$ranks = array_values((new \ReflectionClass(self::class))->getConstants());

			return self::$ranks;
		}
	}