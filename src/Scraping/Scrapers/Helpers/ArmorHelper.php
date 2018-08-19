<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\ArmorRank;
	use App\Game\ArmorType;

	final class ArmorHelper {
		public const ARMOR_TYPE_PHRASES = [
			// -- Head --
			'Headgear' => ArmorType::HEAD,
			'Headpiece' => ArmorType::HEAD,
			'Helm' => ArmorType::HEAD,
			'Hood' => ArmorType::HEAD,
			'Vertex' => ArmorType::HEAD,
			'Goggles' => ArmorType::HEAD,
			'Hat' => ArmorType::HEAD,
			'Mask' => ArmorType::HEAD,
			'Brain' => ArmorType::HEAD,
			'Lobos' => ArmorType::HEAD,
			'Crown' => ArmorType::HEAD,
			'Glare' => ArmorType::HEAD,
			'Horn' => ArmorType::HEAD,
			'Circlet' => ArmorType::HEAD,
			'Gorget' => ArmorType::HEAD,
			'Spectacles' => ArmorType::HEAD,
			'Eyepatch' => ArmorType::HEAD,
			'Shades' => ArmorType::HEAD,
			'Faux Felyne' => ArmorType::HEAD,
			'Head' => ArmorType::HEAD,
			'Taroth\'s Fury' => ArmorType::HEAD,
			'Hair' => ArmorType::HEAD,

			// -- Chest --
			'Mail' => ArmorType::CHEST,
			'Vest' => ArmorType::CHEST,
			'Thorax' => ArmorType::CHEST,
			'Muscle' => ArmorType::CHEST,
			'Suit' => ArmorType::CHEST,
			'Jacket' => ArmorType::CHEST,
			'Hide' => ArmorType::CHEST,
			'Cista' => ArmorType::CHEST,
			'Armor' => ArmorType::CHEST,
			'Taroth\'s Ire' => ArmorType::CHEST,
			'Dante\'s Coat' => ArmorType::CHEST,

			// -- Gloves --
			'Gloves' => ArmorType::GLOVES,
			'Vambraces' => ArmorType::GLOVES,
			'Guards' => ArmorType::GLOVES,
			'Braces' => ArmorType::GLOVES,
			'Brachia' => ArmorType::GLOVES,
			'Grip' => ArmorType::GLOVES,
			'Longarms' => ArmorType::GLOVES,
			'Claws' => ArmorType::GLOVES,
			'Cuffs' => ArmorType::GLOVES,
			'Taroth\'s Rage' => ArmorType::GLOVES,

			// -- Waist --
			'Belt' => ArmorType::WAIST,
			'Coil' => ArmorType::WAIST,
			'Elytra' => ArmorType::WAIST,
			'Bowels' => ArmorType::WAIST,
			'Hoop' => ArmorType::WAIST,
			'Spine' => ArmorType::WAIST,
			'Cocoon' => ArmorType::WAIST,
			'Coat' => ArmorType::WAIST,
			'Taroth\'s Malice' => ArmorType::WAIST,

			// -- Legs --
			'Trousers' => ArmorType::LEGS,
			'Greaves' => ArmorType::LEGS,
			'Boots' => ArmorType::LEGS,
			'Crura' => ArmorType::LEGS,
			'Heel' => ArmorType::LEGS,
			'Heels' => ArmorType::LEGS,
			'Leg Guards' => ArmorType::LEGS,
			'Spurs' => ArmorType::LEGS,
			'Crus' => ArmorType::LEGS,
			'Pants' => ArmorType::LEGS,
			'Taroth\'s Wrath' => ArmorType::LEGS,
		];

		public const ARMOR_SUFFIX_MAP = [
			'α' => 'Alpha',
			'β' => 'Beta',
			'γ' => 'Gamma',
		];

		/**
		 * @var array[]
		 */
		private static $armorNameCache = [];

		/**
		 * @param string $name
		 *
		 * @return string
		 */
		public static function replaceSuffixSymbol(string $name): string {
			return str_replace(array_keys(self::ARMOR_SUFFIX_MAP), self::ARMOR_SUFFIX_MAP, $name);
		}

		/**
		 * @param string $name
		 *
		 * @return string
		 */
		public static function getRank(string $name): string {
			$tail = substr($name, strrpos($name, ' ') + 1);

			foreach (self::ARMOR_SUFFIX_MAP as $suffix) {
				if ($tail === $suffix)
					return ArmorRank::HIGH;
			}

			return ArmorRank::LOW;
		}

		/**
		 * @param string $rawName
		 *
		 * @return array
		 */
		public static function parseArmorName(string $rawName): array {
			if (isset(self::$armorNameCache[$rawName]))
				return self::$armorNameCache[$rawName];

			$cleanedName = self::replaceSuffixSymbol($rawName);

			$parts = array_filter(array_map(function(string $part): string {
				return trim($part);
			}, explode(' ', $cleanedName)));

			$partCount = sizeof($parts);

			if (in_array($parts[$partCount - 1], self::ARMOR_SUFFIX_MAP)) {
				$rank = array_pop($parts);

				--$partCount;
			} else
				$rank = '';

			$armorType = null;
			$partOffsetMax = $partCount - 1;

			foreach (self::ARMOR_TYPE_PHRASES as $phrase => $type) {
				$consumeCount = substr_count($phrase, ' ');

				// If we'd need to consume more of the array than there are pieces, this can't possibly be our match
				if ($consumeCount > $partCount)
					continue;

				$candidate = implode(' ', array_slice($parts, $partOffsetMax - $consumeCount));

				if ($candidate === $phrase) {
					$armorType = $type;

					break;
				}
			}

			if ($armorType === null)
				throw new \RuntimeException('Could not determine armor type from name: ' . $rawName);

			return self::$armorNameCache[$rawName] = [
				trim(implode(' ', $parts) . ' ' . $rank),
				$armorType,
			];
		}

		/**
		 * KiranicoArmorHelper constructor.
		 */
		private function __construct() {
		}
	}