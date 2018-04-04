<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

	use Symfony\Component\DomCrawler\Crawler;

	interface WeaponDataParserInterface {
		/**
		 * @param Crawler    $section
		 * @param WeaponData $target
		 *
		 * @return void
		 */
		public function parse(Crawler $section, WeaponData $target): void;
	}