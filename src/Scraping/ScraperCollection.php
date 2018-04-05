<?php
	namespace App\Scraping;

	use Doctrine\Common\Collections\ArrayCollection;
	use Traversable;

	class ScraperCollection implements \IteratorAggregate {
		protected $scrapers;

		/**
		 * ScraperCollection constructor.
		 *
		 * @param ScraperInterface[] $scrapers
		 */
		public function __construct(array $scrapers = []) {
			$this->setScrapers($scrapers);
		}

		/**
		 * Returns all scrapers in the collection. If the optional $keys argument is supplied, only the scrapers
		 * whose type is in the $keys array will be returned.
		 *
		 * @param string[] $keys
		 *
		 * @return ScraperInterface[]
		 */
		public function getScrapers(array $keys = []): array {
			if (!$keys)
				return $this->scrapers;

			return array_filter($this->scrapers, function(ScraperInterface $scraper) use ($keys): bool {
				return in_array($scraper->getType(), $keys);
			});
		}

		/**
		 * @param ScraperInterface[] $scrapers
		 *
		 * @return $this
		 */
		public function setScrapers(array $scrapers) {
			$this->scrapers = [];

			foreach ($scrapers as $scraper)
				$this->addScraper($scraper);

			return $this;
		}

		/**
		 * @param string $key
		 *
		 * @return ScraperInterface|null
		 */
		public function getScraper(string $key): ?ScraperInterface {
			return $this->scrapers[$key] ?? null;
		}

		/**
		 * @param ScraperInterface $scraper
		 *
		 * @return $this
		 */
		public function addScraper(ScraperInterface $scraper) {
			$this->scrapers[$scraper->getType()] = $scraper;

			return $this;
		}

		/**
		 * @return int
		 */
		public function count(): int {
			return sizeof($this->scrapers);
		}

		/**
		 * {@inheritdoc}
		 */
		public function getIterator(): \ArrayIterator {
			return new \ArrayIterator($this->scrapers);
		}
	}