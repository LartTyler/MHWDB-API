<?php
	namespace App\Scraping\Configurations;

	use App\Scraping\Configuration;
	use Http\Client\Common\HttpMethodsClient;
	use Http\Message\UriFactory;

	class GithubConfiguration extends Configuration {
		/**
		 * CarlosFdezMHWorldDataConfiguration constructor.
		 *
		 * @param HttpMethodsClient|null $httpClient
		 * @param UriFactory|null        $uriFactory
		 */
		public function __construct(HttpMethodsClient $httpClient = null, UriFactory $uriFactory = null) {
			parent::__construct('https://raw.githubusercontent.com', $httpClient, $uriFactory);
		}
	}