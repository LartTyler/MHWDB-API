<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\WeaponType;

	final class MHWGWeaponTreeHelper {
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
		 * These paths are for each of the Kulve Taroth relic weapons, 4 per weapon type
		 */
		const KULVE_TAROTH_WEAPON_PATHS = [
			// region Kulve Taroth Weapons
			'/ida/232663.html',
			'/ida/233195.html',
			'/ida/232664.html',
			'/ida/232665.html',
			'/ida/233196.html',
			'/ida/232666.html',
			'/ida/232667.html',
			'/ida/232668.html',
			'/ida/233198.html',
			'/ida/232669.html',
			'/ida/232670.html',
			'/ida/232671.html',
			'/ida/233199.html',
			'/ida/232672.html',
			'/ida/232673.html',
			'/ida/232674.html',
			'/ida/232675.html',
			'/ida/232676.html',
			'/ida/232677.html',
			'/ida/233200.html',
			'/ida/232678.html',
			'/ida/232679.html',
			'/ida/232680.html',
			'/ida/233201.html',
			'/ida/232681.html',
			'/ida/232682.html',
			'/ida/233202.html',
			'/ida/232683.html',
			'/ida/232726.html',
			'/ida/232684.html',
			'/ida/233203.html',
			'/ida/232685.html',
			'/ida/232686.html',
			'/ida/232727.html',
			'/ida/233204.html',
			'/ida/232687.html',
			'/ida/232688.html',
			'/ida/232689.html',
			'/ida/232729.html',
			'/ida/233205.html',
			'/ida/232690.html',
			'/ida/232691.html',
			'/ida/232692.html',
			'/ida/232730.html',
			'/ida/232693.html',
			'/ida/232694.html',
			'/ida/232695.html',
			'/ida/233206.html',
			'/ida/232696.html',
			'/ida/232697.html',
			'/ida/232698.html',
			'/ida/233207.html',
			'/ida/232699.html',
			'/ida/232700.html',
			'/ida/232701.html',
			'/ida/232731.html',
			'/ida/233208.html',
			'/ida/233197.html',
			'/ida/232702.html',
			'/ida/232703.html',
			'/ida/232704.html',
			// endregion
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