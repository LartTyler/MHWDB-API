<?php
	namespace App\Scraping\Configurations;

	use App\Scraping\Configuration;
	use Http\Client\Common\HttpMethodsClient;
	use Http\Message\UriFactory;

	class KiranicoConfiguration extends Configuration {
		/**
		 * KiranicoConfiguration constructor.
		 *
		 * @param HttpMethodsClient|null $httpClient
		 * @param UriFactory|null        $uriFactory
		 */
		public function __construct(HttpMethodsClient $httpClient = null, UriFactory $uriFactory = null) {
			parent::__construct('https://mhworld.kiranico.com', $httpClient, $uriFactory);
		}
	}