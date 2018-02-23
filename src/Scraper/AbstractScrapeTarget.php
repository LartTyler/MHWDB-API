<?php
	namespace App\Scraper;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Http\Client\Common\HttpMethodsClient;
	use Http\Discovery\HttpClientDiscovery;
	use Http\Discovery\MessageFactoryDiscovery;
	use Psr\Http\Message\UriInterface;

	abstract class AbstractScrapeTarget implements ScrapeTargetInterface {
		/**
		 * @var UriInterface
		 */
		protected $baseUri;

		/**
		 * @var HttpMethodsClient
		 */
		protected $httpClient;

		/**
		 * @var ScraperInterface[]
		 */
		protected $scrapers = [];

		/**
		 * AbstractScrapeTarget constructor.
		 *
		 * @param UriInterface           $baseUri
		 * @param HttpMethodsClient|null $httpClient
		 */
		public function __construct(UriInterface $baseUri, HttpMethodsClient $httpClient = null) {
			$this->baseUri = $baseUri;

			if (!$httpClient)
				$httpClient = new HttpMethodsClient(HttpClientDiscovery::find(), MessageFactoryDiscovery::find());

			$this->httpClient = $httpClient;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getBaseUri(): UriInterface {
			return $this->baseUri;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getHttpClient(): HttpMethodsClient {
			return $this->httpClient;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getScraper(string $type): ?ScraperInterface {
			if (!isset($this->scrapers[$type]))
				return null;

			return $this->scrapers[$type];
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return $this
		 */
		public function addScraper(ScraperInterface $scraper): ScrapeTargetInterface {
			$this->scrapers[$scraper->getType()] = $scraper;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return ScraperInterface[]
		 */
		public function getScrapers(): array {
			return $this->scrapers;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return string[]
		 */
		public function getTypes(): array {
			return array_keys($this->scrapers);
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return EntityInterface[]
		 */
		public function scrape(): array {
			$results = [];

			foreach ($this->getScrapers() as $scraper)
				$results = array_merge($results, $scraper->scrape());

			return $results;
		}
	}