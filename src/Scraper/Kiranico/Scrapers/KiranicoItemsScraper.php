<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Item;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoItemsScraper extends AbstractScraper {
		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * KiranicoItemsScraper constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param RegistryInterface    $registry
		 */
		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $registry) {
			parent::__construct($target, ScraperType::ITEMS);

			$this->manager = $registry->getManager();
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(): void {
			$uri = $this->target->getBaseUri()->withPath('/item');
			$response = $this->target->getHttpClient()->get($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .card-body table td');

			for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
				$node = $crawler->eq($i);

				$name = trim(preg_replace('/\s+/', ' ', $node->text()));
				$link = $node->filter('a')->attr('href');

				$this->process(parse_url($link, PHP_URL_PATH), $name);
			}
		}

		/**
		 * @param string $path
		 * @param string $name
		 *
		 * @return void
		 * @throws \Http\Client\Exception
		 */
		protected function process(string $path, string $name): void {
			$uri = $this->target->getBaseUri()->withPath($path);

			try {
				$response = $this->target->getHttpClient()->get($uri);
			} catch (\Exception $e) {
				sleep(3);

				$response = $this->target->getHttpClient()->get($uri);
			}

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			/**
			 * 0 = Top Navigation
			 * 1 = Metadata (such as title, description, prices, etc.)
			 * 2 = Where to find
			 * 3 = Crafting usage
			 * 4 = Bottom Navigation
			 */
			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .col-lg-9.px-2 .card');

			/**
			 * 0 = Sell price
			 * 1 = Buy price
			 * 2 = Carry amount
			 * 3 = Rarity
			 */
			$metadataNodes = $crawler->eq(1)->filter('.card-footer > div > div');

			$descNode = $crawler->eq(1)->filter('.card-body .media-body p.font-italic')->first();
			$description = StringUtil::clean($descNode->text());

			$rarity = StringUtil::toNumber(StringUtil::clean($metadataNodes->eq(3)->filter('div.lead')->text()));

			$item = $this->manager->getRepository('App:Item')->findOneBy([
				'name' => $name,
			]);

			if (!$item) {
				$item = new Item($name, $description, $rarity);

				$this->manager->persist($item);
			} else
				$item->setDescription($description);

			$sellPrice = StringUtil::toNumber(StringUtil::clean($metadataNodes->eq(0)->filter('div.lead')->text()));
			$item->setSellPrice($sellPrice);

			$buyPrice = StringUtil::toNumber(StringUtil::clean($metadataNodes->eq(1)->filter('div.lead')->text()));
			$item->setBuyPrice($buyPrice);

			$carryLimit = StringUtil::toNumber(StringUtil::clean($metadataNodes->eq(2)->filter('div.lead')->text()));
			$item->setCarryLimit($carryLimit);

			$this->manager->persist($item);
		}
	}