<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Item;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\MHWikiaConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class MHWikiaItemsScraper extends AbstractScraper implements ProgressAwareInterface {
		const ITEM_LIST_PATHS = [
			'/wiki/MHW:_Item_List',
			'/wiki/MHW:_Monster_Material_List',
		];

		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * @var Item[]
		 */
		protected $itemCache = [];

		/**
		 * MHWikiaItemsScraper constructor.
		 *
		 * @param MHWikiaConfiguration $configuration
		 * @param ObjectManager        $manager
		 */
		public function __construct(MHWikiaConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::ITEMS);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function scrape(array $context = []): void {
			$this->progressBar->append(sizeof(self::ITEM_LIST_PATHS));

			foreach (self::ITEM_LIST_PATHS as $path) {
				$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$rows = (new Crawler($response->getBody()->getContents()))->filter('#mw-content-text .linetable tr');

				$this->progressBar->append($rows->count());

				for ($i = 0, $ii = $rows->count(); $i < $ii; $i++) {
					$this->process($rows->eq($i));

					$this->progressBar->advance();
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param Crawler $row
		 *
		 * @return void
		 */
		protected function process(Crawler $row) {
			/**
			 * For items:
			 * 0 = Icon
			 * 1 = Name
			 * 2 = Rarity
			 * 3 = Max carry
			 * 4 = Value
			 * 5 = Obtaining
			 *
			 * For materials:
			 * 0 = Icon
			 * 1 = Name
			 * 2 = Rarity
			 * 3 = Value
			 * 4 = Description
			 */
			$cells = $row->filter('td');

			// If the first cell meets either of the following criteria, we're on a section heading, and
			// need to skip it. A section heading is identified by:
			//     - The first cell has the `colspan` attribute (in the case of items)
			//     - The first cell contains the text "Icon" (in the case of materials)
			$firstCell = $cells->eq(0);

			if ($firstCell->attr('colspan') || StringUtil::clean($firstCell->text()) === 'Icon')
				return;

			$name = explode('<br>', StringUtil::clean($cells->eq(1)->html()))[0];
			$rarity = (int)StringUtil::clean($cells->eq(2)->text());

			$isMaterial = $cells->count() === 5;

			if ($isMaterial)
				$description = StringUtil::clean($cells->last()->text());
			else
				$description = '';

			$item = $this->getItem($name);

			if ($item) {
				$item->setRarity($rarity);

				if ($description)
					$item->setDescription($description);
			} else {
				$item = new Item($name, $description, $rarity);

				$this->manager->persist($item);
				$this->itemCache[$name] = $item;
			}

			$value = StringUtil::toNumber(StringUtil::clean($cells->eq($isMaterial ? 3 : 4)->text()));
			$item->setValue($value);

			$item->setCarryLimit(0);

			if (!$isMaterial) {
				$limit = StringUtil::clean($cells->eq(3)->text());
				$limit = (int)substr($limit, 1);

				$item->setCarryLimit($limit);
			}
		}

		/**
		 * @param string $name
		 *
		 * @return Item|null
		 */
		protected function getItem(string $name): ?Item {
			if (array_key_exists($name, $this->itemCache))
				return $this->itemCache[$name];

			return $this->itemCache[$name] = $this->manager->getRepository('App:Item')->findOneBy([
				'name' => $name,
			]);
		}
	}