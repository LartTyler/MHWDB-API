<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\ArmorType;

	final class KiranicoArmorHelper {
		private const ARMOR_TYPE_PHRASES = [
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

		/**
		 * @param string $rawName
		 *
		 * @return array
		 */
		public static function parseArmorName(string $rawName): array {
			/**
			 * Does a few things:
			 *
			 * 1. Replaces any consecutive whitespace characters with a single space
			 * 2. Corrects any typos present on the scrape target
			 */
			$cleanedName = str_replace([
				'Apha',
				'Barchia',
				'Dodogame',
				'Mossswine',
			], [
				'Alpha',
				'Brachia',
				'Dodogama',
				'Mosswine',
			], preg_replace('/\s+/', ' ', $rawName));

			$parts = array_filter(array_map(function(string $part): string {
				return trim($part);
			}, explode(' ', $cleanedName)));

			$partCount = sizeof($parts);

			if (in_array($parts[$partCount - 1], ['Alpha', 'Beta'])) {
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

			return [
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