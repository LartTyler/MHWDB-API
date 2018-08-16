<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\WeaponType;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	final class MHWikiaHelper {
		const WEAPON_TREE_PATHS = [
			WeaponType::GREAT_SWORD => '/wiki/MHW:_Great_Sword_Weapon_Tree',
			WeaponType::LONG_SWORD => '/wiki/MHW:_Long_Sword_Weapon_Tree',
			WeaponType::SWORD_AND_SHIELD => '/wiki/MHW:_Sword_and_Shield_Weapon_Tree',
			WeaponType::DUAL_BLADES => '/wiki/MHW:_Dual_Blades_Weapon_Tree',
			WeaponType::HAMMER => '/wiki/MHW:_Hammer_Weapon_Tree',
			WeaponType::HUNTING_HORN => '/wiki/MHW:_Hunting_Horn_Weapon_Tree',
			WeaponType::LANCE => '/wiki/MHW:_Lance_Weapon_Tree',
			WeaponType::GUNLANCE => '/wiki/MHW:_Gunlance_Weapon_Tree',
			WeaponType::SWITCH_AXE => '/wiki/MHW:_Switch_Axe_Weapon_Tree',
			WeaponType::CHARGE_BLADE => '/wiki/MHW:_Charge_Blade_Weapon_Tree',
			WeaponType::INSECT_GLAIVE => '/wiki/MHW:_Insect_Glaive_Weapon_Tree',
			WeaponType::LIGHT_BOWGUN => '/wiki/MHW:_Light_Bowgun_Weapon_Tree',
			WeaponType::HEAVY_BOWGUN => '/wiki/MHW:_Heavy_Bowgun_Weapon_Tree',
			WeaponType::BOW => '/wiki/MHW:_Bow_Weapon_Tree',
		];

		/**
		 * MHWikiaHelper constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param Crawler       $nodes
		 * @param callable|null $keyExtractor
		 * @param callable|null $valueExtractor
		 *
		 * @return string[]
		 */
		public static function parseHtmlToKeyValuePairs(
			Crawler $nodes,
			callable $keyExtractor = null,
			callable $valueExtractor = null
		): array {
			$keyExtractor = $keyExtractor ?? function(Crawler $node): string {
					$key = StringUtil::camelize(StringUtil::clean($node->children()->first()->text()));

					return rtrim($key, ':');
				};

			$valueExtractor = $valueExtractor ?? function(Crawler $node): ?string {
					$value = StringUtil::clean($node->children()->last()->text());

					if (!$value || $value === 'N/A')
						return null;

					return $value;
				};

			$values = [];

			for ($i = 0, $ii = $nodes->count(); $i < $ii; $i++) {
				$node = $nodes->eq($i);
				$key = call_user_func($keyExtractor, $node);

				$values[$key] = call_user_func($valueExtractor, $node, $key);
			}

			return $values;
		}
	}