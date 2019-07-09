<?php
	namespace App\Game;

	final class MonsterSpecies {
		public const BIRD_WYVERN = 'bird wyvern';
		public const BRUTE_WYVERN = 'brute wyvern';
		public const ELDER_DRAGON = 'elder dragon';
		public const FANGED_WYVERN = 'fanged wyvern';
		public const FISH = 'fish';
		public const FLYING_WYVERN = 'flying wyvern';
		public const HERBIVORE = 'herbivore';
		public const LYNIAN = 'lynian';
		public const NEOPTERON = 'neopteron';
		public const PISCINE_WYVERN = 'piscine wyvern';
		public const RELICT = 'relict';
		public const WINGDRAKE = 'wingdrake';

		/**
		 * @var string[]|null
		 */
		private static $species = null;

		/**
		 * MonsterSpecies constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$species === null)
				self::$species = array_values((new \ReflectionClass(self::class))->getConstants());

			return self::$species;
		}

		/**
		 * @param string $string
		 *
		 * @return bool
		 */
		public static function isValid(string $string): bool {
			return in_array($string, self::all());
		}
	}