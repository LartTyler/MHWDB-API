<?php
	namespace App\Scraping;

	interface ScraperInterface {
		/**
		 * Defines an array of subtypes that should be scraped.
		 */
		const CONTEXT_SUBTYPES = 'subtypes';

		/**
		 * Returns the type of the scraper.
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * @param array $context
		 *
		 * @return void
		 */
		public function scrape(array $context = []): void;
	}