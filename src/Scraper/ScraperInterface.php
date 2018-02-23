<?php
	namespace App\Scraper;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	interface ScraperInterface {
		/**
		 * Returns the type of the scraper. Must be one of the {@see ScraperType} class constants.
		 *
		 * @return string
		 * @see ScraperType
		 */
		public function getType(): string;

		/**
		 * Scrapes the resource, adding any new entities to the database, and updating existing ones.
		 *
		 * @return \Generator|EntityInterface[]
		 */
		public function scrape(): \Generator;
	}