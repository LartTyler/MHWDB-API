<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Armor;
	use App\Game\ArmorType;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoArmorScraper extends AbstractScraper {
		private const HIGH_RANK_SUFFIX_TRANSLATIONS = [
			'Alpha' => 'α',
			'Beta' => 'β',
		];

		private const ARMOR_TYPE_POSITIONS = [
			ArmorType::HEAD,
			ArmorType::CHEST,
			ArmorType::GLOVES,
			ArmorType::WAIST,
			ArmorType::LEGS,
		];

		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var EntityManagerInterface
		 */
		protected $manager;

		/**
		 * KiranicoArmorScraper constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param RegistryInterface    $registry
		 */
		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $registry) {
			parent::__construct($target, ScraperType::ARMOR);

			$this->manager = $registry->getManager();
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(): void {
			$uri = $this->target->getBaseUri()->withPath('/armor');
			$response = $this->target->getHttpClient()->get($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .tab-content .card');

			for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
				$setPieceNodes = $crawler->eq($i)->filter('.card-body table')->eq(0)->filter('tr');

				for ($j = 0, $jj = $setPieceNodes->count(); $j < $jj; $j++) {
					$link = $setPieceNodes->eq($j)->filter('a')->attr('href');

					$this->process(parse_url($link, PHP_URL_PATH), self::ARMOR_TYPE_POSITIONS[$i]);
				}
			}
		}

		protected function process(string $path, string $armorType) {
			$uri = $this->target->getBaseUri()->withPath($path);
			$response = $this->target->getHttpClient()->get($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);
			
			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .col-lg-9.px-2')
				->children();

			$name = trim($crawler->filter('.card .media h1[itemprop=name]')->text());
			$name = str_replace(array_keys(self::HIGH_RANK_SUFFIX_TRANSLATIONS),
				self::HIGH_RANK_SUFFIX_TRANSLATIONS, $name);

			/** @var Armor|null $armor */
			$armor = $this->manager->getRepository('App:Armor')->findOneBy([
				'name' => $name,
			]);

			if (!$armor) {
				$armor = new Armor($name, $armorType);

				$this->manager->persist($armor);
			} else {
				$armor->setAttributes([]);
				$armor->getSkills()->clear();
			}

			$attributeNodes = $crawler->filter('.row.no-gutters')->children();

			$elemResists = $attributeNodes->eq(0)->filter('.card-body table tr');

			for ($i = 0, $ii = $elemResists->count(); $i < $ii; $i++) {
				$children = $elemResists->eq($i)->children();
				$elemText = trim($children->first()->text());

				$elem = trim(substr($elemText, strrpos($elemText, ' ') + 1));
				$value = trim($children->last()->text());

				if (strpos($value, '+') === 0)
					$value = substr($value, 1);

				$value = (int)$value;

				if ($value === 0)
					continue;

				$armor->setAttribute('resist' . $elem, $value);
			}

			$skills = $attributeNodes->eq(1)->filter('.card-body table tr');

			for ($i = 0, $ii = $skills->count(); $i < $ii; $i++) {
				$children = $skills->eq($i)->children();

				$skillName = trim($children->first()->text());

				$skill = $this->manager->getRepository('App:Skill')->findOneBy([
					'name' => $skillName,
				]);

				if (!$skill)
					throw new \RuntimeException($skillName . ' is not a known skill');

				$skillRank = trim($children->last()->text());

				if (strpos($skillRank, '+') === 0)
					$skillRank = substr($skillRank, 1);

				$rank = $skill->getRank((int)$skillRank);

				if (!$rank)
					throw new \RuntimeException($skillName . ' has no rank labelled "' . $skillRank . '"');

				$armor->getSkills()->add($rank);
			}

			// We sleep to avoid hitting the target too frequently
			sleep(3);
		}
	}