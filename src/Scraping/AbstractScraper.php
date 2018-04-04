<?php
	namespace App\Scraping;

	use Psr\Http\Message\ResponseInterface;
	use Psr\Http\Message\UriInterface;

	abstract class AbstractScraper implements ScraperInterface {
		/**
		 * @var Configuration
		 */
		protected $configuration;

		/**
		 * @var string
		 */
		protected $type;

		/**
		 * AbstractScraper constructor.
		 *
		 * @param Configuration $configuration
		 * @param string        $type
		 */
		public function __construct(Configuration $configuration, string $type) {
			$this->configuration = $configuration;
			$this->type = $type;
		}

		/**
		 * @return Configuration
		 */
		public function getConfiguration(): Configuration {
			return $this->configuration;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * Attempts to retrieve the specified URI. If an exception occurs, this method will retry after a short delay.
		 *
		 * If the second GET attempt also results in an exception, the exception will not be caught.
		 *
		 * @param UriInterface $uri
		 *
		 * @return ResponseInterface
		 */
		public function getWithRetry(UriInterface $uri): ResponseInterface {
			try {
				return $this->configuration->getHttpClient()->get($uri);
			} catch (\Exception $e) {
				sleep(3);

				return $this->configuration->getHttpClient()->get($uri);
			}
		}
	}