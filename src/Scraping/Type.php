<?php
	namespace App\Scraping;

	final class Type {
		const ARMOR = 'armor';
		const CHARMS = 'charms';
		const CHARM_RARITY = 'charm-rarity';
		const DECORATIONS = 'decorations';
		const ITEMS = 'items';
		const SKILLS = 'skills';
		const WEAPONS = 'weapons';
		const ELDERSEAL = 'elderseal';
		const ARMOR_SET_BONUS = 'armor-set-bonus';
		const ARMOR_DEFENSE = 'armor-defense';
		const WEAPON_ICONS = 'weapon-icons';
		const WEAPON_IMAGES = 'weapon-images';
		const MOTION_VALUES = 'motion-values';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * Type constructor.
		 */
		private function __construct() {
		}

		public static function getTypes(): array {
			if (self::$types === null)
				self::$types = (new \ReflectionClass(self::class))->getConstants();

			return self::$types;
		}
	}