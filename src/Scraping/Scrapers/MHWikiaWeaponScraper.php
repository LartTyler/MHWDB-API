<?php
	namespace App\Scraping\Scrapers;

	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\MHWikiaConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\ScraperInterface;
	use App\Scraping\Scrapers\Helpers\MHWikiaHelper;
	use App\Scraping\Type;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class MHWikiaWeaponScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * MHWikiaWeaponScraper constructor.
		 *
		 * @param MHWikiaConfiguration $configuration
		 * @param ObjectManager        $manager
		 */
		public function __construct(MHWikiaConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::WEAPONS);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function scrape(array $context = []): void {
			$subtypes = $context[ScraperInterface::CONTEXT_SUBTYPES] ?? [];

			$this->progressBar->append($subtypes ? sizeof($subtypes) : sizeof(MHWikiaHelper::WEAPON_TREE_PATHS));

			foreach (MHWikiaHelper::WEAPON_TREE_PATHS as $weaponType => $path) {
				if ($subtypes && !in_array($weaponType, $subtypes))
					continue;

				$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$table = (new Crawler($response->getBody()->getContents()))
					->filter('#mw-content-text .wikitable.hover');

				if (!$table->count())
					throw new \RuntimeException('Could not find weapon tree table');

				$rows = $table->first()->filter('tr');

				for ($i = 0, $ii = $rows->count(); $i < $ii; $i++) {
					$link = $rows->eq($i)->filter('td:first-child a')->last();

					if (!$link->count())
						continue;

					$this->process(parse_url($link->attr('href'), PHP_URL_PATH), $weaponType);
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string $path
		 * @param string $weaponType
		 */
		public function process(string $path, string $weaponType): void {
			// TODO Implement per-page scraping instructions
		}
	}