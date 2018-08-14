<?php
	namespace App\Scraping\Scrapers;

	use App\Scraping\AbstractScraper;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use Psr\Http\Message\UriInterface;

	abstract class AbstractCarlosFdezMHWorldDataScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @param string $path
		 *
		 * @return UriInterface
		 */
		protected function getUriWithPath(string $path): UriInterface {
			return $this->configuration->getBaseUri()->withPath('/gatheringhallstudios/MHWorldData/master/source_data/' .
				ltrim($path, '/'));
		}
	}