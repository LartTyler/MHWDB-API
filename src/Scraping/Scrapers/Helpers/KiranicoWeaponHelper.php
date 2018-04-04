<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Game\WeaponType;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\AmmoWeaponDataParser;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\CraftingWeaponDataParser;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\MainWeaponDataParser;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\MaterialWeaponDataParser;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\ParserType;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponData;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataParserInterface;
	use Symfony\Component\DomCrawler\Crawler;

	final class KiranicoWeaponHelper {
		private const LAYOUTS = [
			'' => [
				ParserType::MAIN => 1,
				ParserType::UPGRADE => 2,
				ParserType::MATERIALS => 3,
			],
			WeaponType::LIGHT_BOWGUN => [
				ParserType::MAIN => 1,
				ParserType::AMMO => 2,
				ParserType::UPGRADE => 3,
				ParserType::MATERIALS => 4,
			],
			WeaponType::HEAVY_BOWGUN => [
				ParserType::MAIN => 1,
				ParserType::AMMO => 2,
				ParserType::UPGRADE => 3,
				ParserType::MATERIALS => 4,
			],
		];

		/**
		 * @var WeaponDataParserInterface[]|null
		 */
		private static $parsers = null;

		/**
		 * @param Crawler $sections
		 * @param string  $weaponType
		 *
		 * @return WeaponData
		 */
		public static function parseSectionData(Crawler $sections, string $weaponType): WeaponData {
			self::initParsers();

			$data = new WeaponData();

			if (isset(self::LAYOUTS[$weaponType]))
				$layout = self::LAYOUTS[$weaponType];
			else
				$layout = self::LAYOUTS[''];

			foreach (self::$parsers as $key => $parser) {
				if (!isset($layout[$key]))
					continue;

				$parser->parse($sections->eq($layout[$key]), $data);
			}

			return $data;
		}

		/**
		 * KiranicoWeaponHelper constructor.
		 */
		private function __construct() {
		}

		private static function initParsers(): void {
			if (self::$parsers !== null)
				return;

			self::$parsers = [
				ParserType::AMMO => new AmmoWeaponDataParser(),
				ParserType::MAIN => new MainWeaponDataParser(),
				ParserType::MATERIALS => new MaterialWeaponDataParser(),
				ParserType::UPGRADE => new CraftingWeaponDataParser(),
			];
		}
	}