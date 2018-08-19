<?php
	namespace App\Game;

	final class AmmoType {
		const NORMAL = 'normal';
		const PIERCING = 'piercing';
		const SPREAD = 'spread';
		const STICKY = 'sticky';
		const CLUSTER = 'cluster';
		const RECOVER = 'recover';
		const POISON = 'poison';
		const PARALYSIS = 'paralysis';
		const SLEEP = 'sleep';
		const EXHAUST = 'exhaust';
		const FLAMING = 'flaming';
		const WATER = 'water';
		const FREEZE = 'freeze';
		const THUNDER = 'thunder';
		const DRAGON = 'dragon';
		const SLICING = 'slicing';
		const WYVERN = 'wyvern';
		const DEMON = 'demon';
		const ARMOR = 'armor';
		const TRANQ = 'tranq';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * AmmoType constructor.
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