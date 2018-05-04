<?php
	namespace App\Game;

	final class Attribute {
		// -- General Attributes --
		const DEFENSE = 'defense';
		const HEALTH = 'health';
		const REQUIRED_GENDER = 'requiredGender';
		const STAMINA = 'stamina';

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

		/**
		 * @deprecated Use {@see WeaponElement} instead. Will be removed on 2018-05-12.
		 */
		const ELEM_TYPE = 'elementType';

		/**
		 * @deprecated Use {@see WeaponElement} instead. Will be removed on 2018-05-12.
		 */
		const ELEM_DAMAGE = 'elementDamage';

		/**
		 * @deprecated Use {@see WeaponElement} instead. Will be removed on 2018-05-12.
		 */
		const ELEM_HIDDEN = 'elementHidden';

		/**
		 * @deprecated Use {@see WeaponElement} instead. Will be removed on 2018-05-12.
		 */
		const ELEM_TYPE_2 = 'elementType2';

		/**
		 * @deprecated Use {@see WeaponElement} instead. Will be removed on 2018-05-12.
		 */
		const ELEM_DAMAGE_2 = 'elementDamage2';

		/**
		 * @deprecated Use {@see WeaponElement} instead. Will be removed on 2018-05-12.
		 */
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