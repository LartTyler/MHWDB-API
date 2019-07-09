<?php
	namespace App\Game;

	final class Element {
		const FIRE = 'fire';
		const WATER = 'water';
		const ICE = 'ice';
		const THUNDER = 'thunder';
		const DRAGON = 'dragon';
		const BLAST = 'blast';
		const POISON = 'poison';
		const SLEEP = 'sleep';
		const STUN = 'stun';
		const PARALYSIS = 'paralysis';

		const ALL = [
			self::FIRE,
			self::WATER,
			self::ICE,
			self::THUNDER,
			self::DRAGON,
			self::BLAST,
			self::POISON,
			self::SLEEP,
			self::STUN,
			self::PARALYSIS,
		];

		const DAMAGE = [
			self::FIRE,
			self::WATER,
			self::ICE,
			self::THUNDER,
			self::DRAGON,
		];

		const STATUS = [
			self::BLAST,
			self::POISON,
			self::SLEEP,
			self::STUN,
			self::PARALYSIS,
		];

		/**
		 * Element constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return array
		 */
		public static function all(): array {
			return self::ALL;
		}

		/**
		 * @return array
		 */
		public static function getAllowedWeaponElements() {
			return array_merge(
				self::DAMAGE,
				[
					self::BLAST,
					self::POISON,
					self::SLEEP,
					self::PARALYSIS,
				]
			);
		}

		/**
		 * @param string $string
		 *
		 * @return bool
		 */
		public static function isValid(string $string): bool {
			return in_array($string, self::ALL);
		}
	}