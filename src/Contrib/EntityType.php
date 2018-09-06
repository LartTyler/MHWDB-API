<?php
	namespace App\Contrib;

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

		public const ENTITY_CLASS_MAP = [
			self::AILMENTS => Ailment::class,
			self::ARMORS => Armor::class,
			self::ARMOR_SETS => ArmorSet::class,
			self::CHARMS => Charm::class,
			self::DECORATIONS => Decoration::class,
			self::ITEMS => Item::class,
			self::LOCATIONS => Location::class,
			self::MONSTERS => Monster::class,
			self::MOTION_VALUES => MotionValue::class,
			self::SKILLS => Skill::class,
			self::WEAPONS => Weapon::class,
		];

		/**
		 * EntityType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $value
		 *
		 * @return bool
		 */
		public static function isValid(string $value): bool {
			return isset(self::ENTITY_CLASS_MAP[$value]);
		}
	}