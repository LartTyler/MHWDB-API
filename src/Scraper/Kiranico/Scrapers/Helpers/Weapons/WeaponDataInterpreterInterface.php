<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons;

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