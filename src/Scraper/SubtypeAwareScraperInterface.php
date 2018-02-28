<?php
	namespace App\Scraper;

	interface SubtypeAwareScraperInterface extends ScraperInterface {
		/**
		 * {@inheritdoc}
		 *
		 * @param string[] $subtypes
		 */
		public function scrape(array $subtypes = []): void;
	}