<?php
	namespace App\Game;

	final class RewardConditionType {
		public const CARVE = 'carve';
		public const INVESTIGATION = 'investigation';
		public const MINING = 'mining';
		public const PALICO = 'palico';
		public const PLUNDERBLADE = 'plunderblade';
		public const REWARD = 'reward';
		public const SIEGE_REWARD = 'siege reward';
		public const SHINY = 'shiny';
		public const TRACK = 'track';
		public const WOUND = 'wound';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * RewardConditionType constructor.
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