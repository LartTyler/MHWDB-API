<?php
	namespace App\Scraper;

	interface ScraperInterface {
		/**
		 * Returns the type of the scraper. Must be one of the {@see ScraperType} class constants.
		 *
		 * @return string
		 * @see ScraperType
		 */
		public function getType(): string;

		/**
		 * Scrapes the resource and updates the application state to match what was scraped.
		 *
		 * @return void
		 */
		public function scrape(): void;
	}