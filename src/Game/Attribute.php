<?php
	namespace App\Game;

	final class Attribute {
		// -- General Attributes --
		const HEALTH = 'health';
		const STAMINA = 'stamina';
		const SLOT_RANK_1 = 'slotsRank1';
		const SLOT_RANK_2 = 'slotsRank2';
		const SLOT_RANK_3 = 'slotsRank3';
		const REQUIRED_GENDER = 'requiredGender';

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
		const DEFENSE = 'defense';
		const RES_FIRE = 'resistFire';
		const RES_WATER = 'resistWater';
		const RES_THUNDER = 'resistThunder';
		const RES_ICE = 'resistIce';
		const RES_DRAGON = 'resistDragon';

		/**
		 * Attribute constructor.
		 */
		private function __construct() {
		}
	}