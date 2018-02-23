<?php
	namespace App\Scraper;

	abstract class AbstractScraper implements ScraperInterface {
		/**
		 * @var string
		 */
		protected $type;

		/**
		 * @var ScrapeTargetInterface
		 */
		protected $target;

		/**
		 * AbstractScraper constructor.
		 *
		 * @param ScrapeTargetInterface $target
		 * @param string                $type
		 */
		public function __construct(ScrapeTargetInterface $target, string $type) {
			$this->target = $target;
			$this->type = $type;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getType(): string {
			return $this->type;
		}
	}