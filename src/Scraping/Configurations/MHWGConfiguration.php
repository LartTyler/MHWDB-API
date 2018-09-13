<?php
	namespace App\Scraping\Configurations;

	use App\Scraping\Configuration;
	use Http\Client\Common\HttpMethodsClient;
	use Http\Message\UriFactory;

	class MHWGConfiguration extends Configuration {
		/**
		 * MHWGConfiguration constructor.
		 *
		 * @param HttpMethodsClient|null $httpClient
		 * @param UriFactory|null        $uriFactory
		 */
		public function __construct(?HttpMethodsClient $httpClient = null, ?UriFactory $uriFactory = null) {
			parent::__construct('http://mhwg.org', $httpClient, $uriFactory);
		}
	}