<?php
	namespace App\Game;

	final class Ailment {
		public const BLASTBLIGHT = 'blastblight';
		public const BLEEDING = 'bleeding';
		public const DRAGONBLIGHT = 'dragonblight';
		public const EFFLUVIAL_BUILDUP = 'effluvial buildup';
		public const FIREBLIGHT = 'fireblight';
		public const HEAT = 'heat';
		public const ICEBLIGHT = 'iceblight';
		public const PARALYSIS = 'paralysis';
		public const POISON = 'poison';
		public const SLEEP = 'sleep';
		public const STUN = 'stun';
		public const THUNDERBLIGHT = 'thunderblight';
		public const WATERBLIGHT = 'waterblight';
		public const WIND_PRESSURE = 'wind pressure';

		/**
		 * @var string[]|null
		 */
		private static $ailments = null;

		/**
		 * Ailment constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$ailments === null)
				self::$ailments = array_values((new \ReflectionClass(self::class))->getConstants());

			return self::$ailments;
		}
	}