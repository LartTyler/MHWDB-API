<?php
	namespace App\Game;

	final class WeaponType {
		const GREAT_SWORD = 'great-sword';
		const LONG_SWORD = 'long-sword';
		const SWORD_AND_SHIELD = 'sword-and-shield';
		const DUAL_BLADES = 'dual-blades';
		const HAMMER = 'hammer';
		const HUNTING_HORN = 'hunting-horn';
		const LANCE = 'lance';
		const GUNLANCE = 'gunlance';
		const SWITCH_AXE = 'switch-axe';
		const CHARGE_BLADE = 'charge-blade';
		const INSECT_GLAIVE = 'insect-glaive';
		const LIGHT_BOWGUN = 'light-bowgun';
		const HEAVY_BOWGUN = 'heavy-bowgun';
		const BOW = 'bow';

		/**
		 * @param string $type
		 *
		 * @return bool
		 */
		public static function isMelee(string $type): bool {
			return !self::isRanged($type);
		}

		/**
		 * @param string $type
		 *
		 * @return bool
		 */
		public static function isRanged(string $type): bool {
			return in_array($type, [
				self::LIGHT_BOWGUN,
				self::HEAVY_BOWGUN,
				self::BOW,
			]);
		}

		/**
		 * WeaponType constructor.
		 */
		private function __construct() {
		}
	}