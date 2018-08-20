<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\WeaponType;

	final class MHWGHelper {
		const WEAPON_TREE_MAP = [
			WeaponType::GREAT_SWORD => '/4000.html',
			WeaponType::LONG_SWORD => '/4001.html',
			WeaponType::SWORD_AND_SHIELD => '/4002.html',
			WeaponType::DUAL_BLADES => '/4003.html',
			WeaponType::HAMMER => '/4004.html',
			WeaponType::HUNTING_HORN => '/4005.html',
			WeaponType::LANCE => '/4006.html',
			WeaponType::GUNLANCE => '/4007.html',
			WeaponType::SWITCH_AXE => '/4008.html',
			WeaponType::CHARGE_BLADE => '/4009.html',
			WeaponType::INSECT_GLAIVE => '/4010.html',
			WeaponType::LIGHT_BOWGUN => '/4011.html',
			WeaponType::HEAVY_BOWGUN => '/4012.html',
			WeaponType::BOW => '/4013.html',
		];

		/**
		 * MHWGHelper constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param int $value
		 *
		 * @return int
		 */
		public static function toOldSharpnessValue(int $value): int {
			return (int)(100 * ($value / 400));
		}
	}