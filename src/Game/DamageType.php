<?php
	namespace App\Game;

	final class DamageType {
		const BLUNT = 'blunt';
		const SEVER = 'sever';
		const PROJECTILE = 'projectile';

		const WEAPON_TYPE_MAP = [
			WeaponType::GREAT_SWORD => self::SEVER,
			WeaponType::LONG_SWORD => self::SEVER,
			WeaponType::SWORD_AND_SHIELD => self::SEVER,
			WeaponType::DUAL_BLADES => self::SEVER,
			WeaponType::HAMMER => self::BLUNT,
			WeaponType::HUNTING_HORN => self::BLUNT,
			WeaponType::LANCE => self::SEVER,
			WeaponType::GUNLANCE => self::SEVER,
			WeaponType::SWITCH_AXE => self::SEVER,
			WeaponType::CHARGE_BLADE => self::SEVER,
			WeaponType::INSECT_GLAIVE => self::SEVER,
			WeaponType::LIGHT_BOWGUN => self::PROJECTILE,
			WeaponType::HEAVY_BOWGUN => self::PROJECTILE,
			WeaponType::BOW => self::PROJECTILE,
		];

		/**
		 * @var string[]
		 */
		private static $types = null;

		/**
		 * DamageType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $value
		 *
		 * @return bool
		 */
		public static function isValid(string $value): bool {
			return in_array($value, self::all());
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$types === null)
				self::$types = (new \ReflectionClass(self::class))->getConstants();

			return self::$types;
		}

		/**
		 * @param string $weaponType
		 *
		 * @return string
		 */
		public static function getWeaponDamageType(string $weaponType): string {
			return self::WEAPON_TYPE_MAP[$weaponType];
		}
	}