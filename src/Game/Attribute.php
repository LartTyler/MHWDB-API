<?php
	namespace App\Game;

	final class Attribute {
		// -- General Attributes --
		const HEALTH = 'health';
		const STAMINA = 'stamina';
		const REQUIRED_GENDER = 'requiredGender';

		/**
		 * @deprecated Use {@see WeaponSharpness} instead. Will be removed 2018-05-05.
		 */
		const SLOT_RANK_1 = 'slotsRank1';

		/**
		 * @deprecated Use {@see WeaponSharpness} instead. Will be removed 2018-05-05.
		 */
		const SLOT_RANK_2 = 'slotsRank2';

		/**
		 * @deprecated Use {@see WeaponSharpness} instead. Will be removed 2018-05-05.
		 */
		const SLOT_RANK_3 = 'slotsRank3';

		// -- Skill Attribute Modifiers --
		const SHARP_BONUS = 'sharpnessBonus';
		const RES_ALL = 'resistAll';
		const DAM_FIRE = 'damageFire';
		const DAM_WATER = 'damageWater';
		const DAM_ICE = 'damageIce';
		const DAM_THUNDER = 'damageThunder';
		const DAM_DRAGON = 'damageDragon';

		// -- Weapon Attributes --
		const ATTACK = 'attack';
		const AFFINITY = 'affinity';
		const DAMAGE_TYPE = 'damageType';
		const ELEM_TYPE = 'elementType';
		const ELEM_DAMAGE = 'elementDamage';
		const ELEM_HIDDEN = 'elementHidden';
		const ELEM_TYPE_2 = 'elementType2';
		const ELEM_DAMAGE_2 = 'elementDamage2';
		const ELEM_HIDDEN_2 = 'elementHidden2';
		const ELDERSEAL = 'elderseal';

		// -- Melee Weapon Attributes --
		const SHARP_RED = 'sharpnessRed';
		const SHARP_ORANAGE = 'sharpnessOrange';
		const SHARP_YELLOW = 'sharpnessYellow';
		const SHARP_GREEN = 'sharpnessGreen';
		const SHARP_BLUE = 'sharpnessBlue';
		const SHARP_WHITE = 'sharpnessWhite';

		// -- Insect Glaive Attributes --
		const IG_BOOST_TYPE = 'boostType';

		// -- Gunlance Attributes --
		const GL_SHELLING_TYPE = 'shellingType';

		// -- Switch Axe / Charge Blade Attributes --
		const PHIAL_TYPE = 'phialType';

		// -- Bow Attributes --
		const COATINGS = 'coatings';

		// -- Bowgun Attributes --
		const AMMO_CAPACITIES = 'ammoCapacities';
		const DEVIATION = 'deviation';
		const SPECIAL_AMMO = 'specialAmmo';

		// -- Armor Attributes --

		/**
		 * @deprecated Use {@see ArmorDefenseValues} instead. Will be removed 2018-05-05.
		 */
		const DEFENSE = 'defense';

		/**
		 * @deprecated Use {@see Resistances} instead. Will be removed 2018-05-05.
		 */
		const RES_FIRE = 'resistFire';

		/**
		 * @deprecated Use {@see Resistances} instead. Will be removed 2018-05-05.
		 */
		const RES_WATER = 'resistWater';

		/**
		 * @deprecated Use {@see Resistances} instead. Will be removed 2018-05-05.
		 */
		const RES_THUNDER = 'resistThunder';

		/**
		 * @deprecated Use {@see Resistances} instead. Will be removed 2018-05-05.
		 */
		const RES_ICE = 'resistIce';

		/**
		 * @deprecated Use {@see Resistances} instead. Will be removed 2018-05-05.
		 */
		const RES_DRAGON = 'resistDragon';

		/**
		 * Attribute constructor.
		 */
		private function __construct() {
		}
	}