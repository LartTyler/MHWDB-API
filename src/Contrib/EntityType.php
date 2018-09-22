<?php
	namespace App\Contrib;

	use App\Contrib\Data\AilmentEntityData;
	use App\Contrib\Data\ArmorEntityData;
	use App\Contrib\Data\ArmorSetEntityData;
	use App\Contrib\Data\CharmEntityData;
	use App\Contrib\Data\DecorationEntityData;
	use App\Contrib\Data\ItemEntityData;
	use App\Contrib\Data\LocationEntityData;
	use App\Contrib\Data\MonsterEntityData;
	use App\Contrib\Data\MotionValueEntityData;
	use App\Contrib\Data\SkillEntityData;
	use App\Contrib\Data\WeaponEntityData;
	use App\Entity\Ailment;
	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\Charm;
	use App\Entity\Decoration;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MotionValue;
	use App\Entity\Skill;
	use App\Entity\Weapon;

	final class EntityType {
		public const AILMENTS = 'ailments';
		public const ARMOR = 'armor';
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
			self::ARMOR => ArmorEntityData::class,
			self::ARMOR_SETS => ArmorSetEntityData::class,
			self::CHARMS => CharmEntityData::class,
			self::DECORATIONS => DecorationEntityData::class,
			self::ITEMS => ItemEntityData::class,
			self::LOCATIONS => LocationEntityData::class,
			self::MONSTERS => MonsterEntityData::class,
			self::MOTION_VALUES => MotionValueEntityData::class,
			self::SKILLS => SkillEntityData::class,
			self::WEAPONS => WeaponEntityData::class,
		];

		public const ENTITY_CLASS_MAP = [
			Ailment::class => self::AILMENTS,
			Armor::class => self::ARMOR,
			ArmorSet::class => self::ARMOR_SETS,
			Charm::class => self::CHARMS,
			Decoration::class => self::DECORATIONS,
			Item::class => self::ITEMS,
			Location::class => self::LOCATIONS,
			Monster::class => self::MONSTERS,
			MotionValue::class => self::MOTION_VALUES,
			Skill::class => self::SKILLS,
			Weapon::class => self::WEAPONS,
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
		 * @throws \ReflectionException
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
		 * @throws \ReflectionException
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