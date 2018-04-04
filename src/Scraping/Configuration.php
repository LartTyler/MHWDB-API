<?php
	namespace App\Scraping;

	use Http\Client\Common\HttpMethodsClient;
	use Http\Discovery\HttpClientDiscovery;
	use Http\Discovery\MessageFactoryDiscovery;
	use Http\Discovery\UriFactoryDiscovery;
	use Http\Message\UriFactory;
	use Psr\Http\Message\UriInterface;

	class Configuration {
		/**
		 * @var HttpMethodsClient
		 */
		protected $httpClient;

		/**
		 * @var UriInterface
		 */
		protected $baseUri;

		/**
		 * Configuration constructor.
		 *
		 * @param string                 $baseUri
		 * @param HttpMethodsClient|null $httpClient
		 * @param UriFactory|null        $uriFactory
		 */
		public function __construct(
			string $baseUri,
			HttpMethodsClient $httpClient = null,
			UriFactory $uriFactory = null
		) {
			if (!$httpClient)
				$httpClient = new HttpMethodsClient(HttpClientDiscovery::find(), MessageFactoryDiscovery::find());

			$this->httpClient = $httpClient;
			$this->baseUri = ($uriFactory ?? UriFactoryDiscovery::find())->createUri($baseUri);
		}

		/**
		 * @return HttpMethodsClient
		 */
		public function getHttpClient(): HttpMethodsClient {
			return $this->httpClient;
		}

		/**
		 * @return UriInterface
		 */
		public function getBaseUri(): UriInterface {
			return $this->baseUri;
		}
	}