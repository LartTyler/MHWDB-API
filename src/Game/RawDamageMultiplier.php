<?php
	namespace App\Game;

	/**
	 * Class RawDamageMultiplier
	 *
	 * @package App\Game
	 * @see     https://monsterhunterworld.wiki.fextralife.com/Attack+Power
	 */
	final class RawDamageMultiplier {
		/**
		 * @var int[]
		 */
		protected static $multipliers = [
			WeaponType::GREAT_SWORD => 4.8,
			WeaponType::LONG_SWORD => 3.3,
			WeaponType::SWORD_AND_SHIELD => 1.4,
			WeaponType::DUAL_BLADES => 1.4,
			WeaponType::HAMMER => 5.2,
			WeaponType::HUNTING_HORN => 4.2,
			WeaponType::LANCE => 2.3,
			WeaponType::GUNLANCE => 2.3,
			WeaponType::SWITCH_AXE => 3.5,
			WeaponType::CHARGE_BLADE => 3.6,
			WeaponType::INSECT_GLAIVE => 3.1,
			WeaponType::LIGHT_BOWGUN => 1.3,
			WeaponType::HEAVY_BOWGUN => 1.5,
			WeaponType::BOW => 1.2,
		];

		/**
		 * RawDamageMultiplier constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $type
		 *
		 * @return float
		 */
		public static function get(string $type): float {
			return self::$multipliers[$type];
		}
	}