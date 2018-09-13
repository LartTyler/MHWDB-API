<?php
	namespace App\Contrib;

	use App\Contrib\Data\AilmentEntityData;
	use App\Contrib\Data\ArmorEntityData;
	use App\Contrib\Data\ArmorSetEntityData;
	use App\Contrib\Data\DecorationEntityData;
	use App\Contrib\Data\ItemEntityData;
	use App\Contrib\Data\LocationEntityData;
	use App\Contrib\Data\MonsterEntityData;
	use App\Contrib\Data\MotionValueEntityData;
	use App\Contrib\Data\SkillEntityData;
	use App\Contrib\Data\WeaponEntityData;

	final class EntityType {
		public const AILMENTS = 'ailments';
		public const ARMORS = 'armors';
		public const ARMOR_SETS = 'armor-sets';
		public const CHARMS = 'charms';
		public const DECORATIONS = 'decorations';
		public const ITEMS = 'items';
		public const LOCATIONS = 'locations';
		public const MONSTERS = 'monsters';
		public const MOTION_VALUES = 'motion-values';
		public const SKILLS = 'skills';
		public const WEAPONS = 'weapons';

		public const DATA_CLASS_MAP = [
			self::AILMENTS => AilmentEntityData::class,
			self::ARMORS => ArmorEntityData::class,
			self::ARMOR_SETS => ArmorSetEntityData::class,
			self::DECORATIONS => DecorationEntityData::class,
			self::ITEMS => ItemEntityData::class,
			self::LOCATIONS => LocationEntityData::class,
			self::MONSTERS => MonsterEntityData::class,
			self::MOTION_VALUES => MotionValueEntityData::class,
			self::SKILLS => SkillEntityData::class,
			self::WEAPONS => WeaponEntityData::class,
		];

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * EntityType constructor.
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

		/**
		 * @param string $value
		 *
		 * @return bool
		 */
		public static function isValid(string $value): bool {
			return in_array($value, self::all());
		}

		/**
		 * @param string $type
		 *
		 * @return null|string
		 */
		public static function getDataClass(string $type): ?string {
			return self::DATA_CLASS_MAP[$type] ?? null;
		}
	}