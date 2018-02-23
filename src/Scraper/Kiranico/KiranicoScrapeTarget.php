<?php
	namespace App\Scraper\Kiranico;

	use App\Scraper\AbstractScrapeTarget;
	use Http\Client\Common\HttpMethodsClient;
	use Http\Discovery\UriFactoryDiscovery;
	use Http\Message\UriFactory;

	class KiranicoScrapeTarget extends AbstractScrapeTarget {
		/**
		 * KiranicoScrapeTarget constructor.
		 *
		 * @param UriFactory|null        $uriFactory
		 * @param HttpMethodsClient|null $httpClient
		 */
		public function __construct(UriFactory $uriFactory = null, HttpMethodsClient $httpClient = null) {
			$baseUri = ($uriFactory ?? UriFactoryDiscovery::find())->createUri('https://mhworld.kiranico.com');

			parent::__construct($baseUri, $httpClient);
		}
	}