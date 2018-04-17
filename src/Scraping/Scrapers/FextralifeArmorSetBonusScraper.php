<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\FextralifeConfiguration;
	use App\Scraping\Type;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\EntityManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class FextralifeArmorSetBonusScraper extends AbstractScraper {
		/**
		 * @var ObjectManager|EntityManager
		 */
		protected $manager;

		/**
		 * @var ArmorSetBonus[]
		 */
		protected $bonusCache = [];

		/**
		 * FextralifeArmorSetBonusScraper constructor.
		 *
		 * @param FextralifeConfiguration $configuration
		 * @param ObjectManager           $manager
		 */
		public function __construct(FextralifeConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::ARMOR_SET_BONUS);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 *
		 * @return void
		 */
		public function scrape(array $context = []): void {
			$sets = $this->manager->getRepository('App:ArmorSet')->findAll();

			foreach ($sets as $set) {
				$slug = str_replace(' ', '+', $set->getName()) . '+Armor+Set';

				$uri = $this->configuration->getBaseUri()->withPath('/' . $slug);
				$result = $this->getWithRetry($uri);

				if ($result->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				// #infobox > div.table-responsive > table > tbody > tr:nth-child(8) > td:nth-child(2)
				$node = (new Crawler($result->getBody()->getContents()))
					->filter('#infobox .wiki_table tr:nth-child(8) td:last-child a');

				if ($node->count()) {
					$set->setBonus(null);

					continue;
				}

				$this->process($set, $node->attr('href'), $node->text());
			}
		}

		/**
		 * @param ArmorSet $set
		 * @param string   $path
		 * @param string   $bonusName
		 *
		 * @return void
		 */
		protected function process(ArmorSet $set, string $path, string $bonusName): void {
			$uri = $this->configuration->getBaseUri()->withPath($path);
			$result = $this->getWithRetry($uri);

			if ($result->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$bonus = $this->getBonus($bonusName);

			if (!$bonus) {
				$bonus = new ArmorSetBonus($bonusName);
				$set->setBonus($bonus);

				$this->manager->persist($this->bonusCache[$bonusName] = $bonus);
			} else
				$set->setBonus($bonus);
		}

		/**
		 * @param string $name
		 *
		 * @return ArmorSetBonus|null
		 */
		protected function getBonus(string $name): ?ArmorSetBonus {
			if (isset($this->bonusCache[$name]))
				return $this->bonusCache[$name];

			$bonus = $this->manager->getRepository('App:ArmorSetBonus')->findOneBy([
				'name' => $name,
			]);

			return $this->bonusCache[$name] = $bonus;
		}
	}