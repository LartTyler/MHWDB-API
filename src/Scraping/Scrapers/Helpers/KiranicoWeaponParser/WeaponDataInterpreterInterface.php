<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

	use Symfony\Component\DomCrawler\Crawler;

	interface WeaponDataInterpreterInterface {
		/**
		 * @param Crawler $node
		 *
		 * @return bool
		 */
		public function supports(Crawler $node): bool;

		/**
		 * @param Crawler    $node
		 * @param WeaponData $target
		 *
		 * @return void
		 */
		public function parse(Crawler $node, WeaponData $target): void;
	}