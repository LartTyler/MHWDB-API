<?php
	namespace App\Scraper;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Http\Client\Common\HttpMethodsClient;
	use Psr\Http\Message\UriInterface;

	interface ScrapeTargetInterface {
		/**
		 * Returns the base URI for resources on this scrape target.
		 *
		 * @return UriInterface
		 */
		public function getBaseUri();

		/**
		 * Returns the {@see HttpMethodsClient} used by this scrape target.
		 *
		 * @return HttpMethodsClient
		 */
		public function getHttpClient();

		/**
		 * Returns a scraper of the given type, if it is supported.
		 *
		 * @param string $type
		 *
		 * @return ScraperInterface|null
		 */
		public function getScraper(string $type): ?ScraperInterface;

		/**
		 * @param ScraperInterface $scraper
		 *
		 * @return $this
		 */
		public function addScraper(ScraperInterface $scraper);

		/**
		 * Returns an array of all supported scrapers, keyed by their type.
		 *
		 * @return ScraperInterface[]
		 */
		public function getScrapers(): array;

		/**
		 * Returns an array containing the types supported by the scrape target.
		 *
		 * @return string[]
		 * @see ScraperType
		 */
		public function getTypes(): array;

		/**
		 * Calls the `scrape()` method on all supported scrapers.
		 *
		 * @return void
		 * @see ScraperInterface::scrape()
		 */
		public function scrape(): void;
	}