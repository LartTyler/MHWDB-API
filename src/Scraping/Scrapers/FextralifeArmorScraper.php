<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Armor;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\FextralifeConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\ArmorHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class FextralifeArmorScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * FextralifeArmorScraper constructor.
		 *
		 * @param FextralifeConfiguration $configuration
		 * @param ObjectManager           $manager
		 */
		public function __construct(FextralifeConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::ARMOR);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function scrape(array $context = []): void {
			$uri = $this->getConfiguration()->getBaseUri()->withPath('/Armor');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$armors = (new Crawler($response->getBody()->getContents()))->filter('#wiki-content-block .col-sm-3 a');

			$this->progressBar->append($armors->count());

			for ($i = 0, $ii = $armors->count(); $i < $ii; $i++) {
				$this->processSet($armors->eq($i)->attr('href'));

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string $path
		 */
		protected function processSet(string $path): void {
			$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);
			
			$pieces = (new Crawler($response->getBody()->getContents()))
				->filter('#wiki-content-block > #infobox ~ .row table tr');

			// Starting at 1 to skip the header row
			for ($i = 1, $ii = $pieces->count(); $i < $ii; $i++)
				$this->processPiece($pieces->eq($i)->filter('td:first-child a')->attr('href'));
		}

		/**
		 * @param string $path
		 */
		protected function processPiece(string $path): void {
			$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$contentBlock = (new Crawler($response->getBody()->getContents()))->filter('#wiki-content-block');

			/**
			 * 0 = Name
			 * 1 = Image (handled by another scraper)
			 * 2 = Rarity (in cell 2)
			 * 3 = Defense (handled by another scraper)
			 * 4 = Decoration slots
			 * 5 = Fire resistance
			 * 6 = Water resistance
			 * 7 = Thunder resistance
			 * 8 = Ice resistance
			 * 9 = Dragon resistance
			 * 10 = Skill section label (ignored)
			 * 11 = Skills
			 */
			$infoRows = $contentBlock->filter('#infobox tr');

			[$name, $type] = ArmorHelper::parseArmorName(StringUtil::clean($infoRows->eq(0)->text()));

			$rarity = StringUtil::clean($infoRows->eq(2)->filter('td:last-child')->text());
			$rarity = (int)substr($rarity, strrpos($rarity, ' ') + 1);

			/** @var Armor|null $armor */
			$armor = $this->manager->getRepository('App:Armor')->findOneBy([
				'name' => $name,
			]);

			if (!$armor) {
				$armor = new Armor($name, $type, ArmorHelper::getRank($name), $rarity);

				$this->manager->persist($armor);
			} else {
				$armor
					->setRank(ArmorHelper::getRank($name))
					->setRarity($rarity);
			}
		}
	}