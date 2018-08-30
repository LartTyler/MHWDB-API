<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Ailment;
	use App\Entity\Monster;
	use App\Game\Element;
	use App\Game\MonsterSpecies;
	use App\Game\MonsterType;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\FextralifeConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\FextralifeHelper;
	use App\Scraping\Scrapers\Helpers\HtmlHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class FextralifeMonsterScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		public const CONTEXT_TYPES = 'types';

		public const MONSTER_LIST_URLS = [
			MonsterType::SMALL => '/Small+Monsters',
			MonsterType::LARGE => '/Large+Monsters',
		];

		/**
		 * @var EntityManagerInterface
		 */
		protected $manager;

		/**
		 * FextralifeMonsterScraper constructor.
		 *
		 * @param FextralifeConfiguration $configuration
		 * @param EntityManagerInterface  $manager
		 */
		public function __construct(FextralifeConfiguration $configuration, EntityManagerInterface $manager) {
			parent::__construct($configuration, Type::MONSTERS);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function scrape(array $context = []): void {
			if ($typeContext = ($context[self::CONTEXT_TYPES] ?? null)) {
				$paths = [];

				foreach ($typeContext as $key) {
					if (!isset(self::MONSTER_LIST_URLS[$key]))
						throw new \RuntimeException('Unrecognized monster type: ' . $key);

					$paths[$key] = self::MONSTER_LIST_URLS[$key];
				}
			} else
				$paths = self::MONSTER_LIST_URLS;

			$this->progressBar->append(sizeof($paths));

			foreach ($paths as $monsterType => $path) {
				$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$links = (new Crawler($response->getBody()->getContents()))
					->filter('#wiki-content-block h3')
					->nextAll()
					->reduce(function(Crawler $node): bool {
						return $node->attr('class') === 'row';
					})
					->filter('h4 a')
					->each(function(Crawler $node): string {
						return $node->attr('href');
					});

				$this->progressBar->append(sizeof($links));

				foreach ($links as $link) {
					$this->process($link, $monsterType);

					$this->progressBar->advance();
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string $path
		 * @param string $monsterType
		 *
		 * @return void
		 */
		protected function process(string $path, string $monsterType): void {
			$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = new Crawler($response->getBody()->getContents());

			/**
			 * 0 = Name
			 * 1 = Image
			 * 2 = Type
			 * 3 = Elements
			 * 4 = Ailments
			 * 5 = Weaknesses
			 * 6 = Resistances
			 * 7 = Locations
			 */
			$rows = $crawler->filter('#infobox table tr, #wiki-content-block > .infobox table tr');

			$name = StringUtil::clean($rows->eq(0)->filter('h2')->text());

			if ($name === 'Gastodon')
				$species = MonsterSpecies::HERBIVORE;

			$statsNode = $crawler->filter('#wiki-content-block h3')->first()->nextAll()->first();
			$stats = HtmlHelper::parseHtmlToKeyValuePairs($statsNode->children(), function(Crawler $node): string {
				return strtolower(StringUtil::clean(strtok($node->text(), ':')));
			}, function(Crawler $node): string {
				strtok($node->text(), ':');

				return StringUtil::clean(strtok(''));
			});

			$species = $species ?? FextralifeHelper::cleanSpeciesName($stats['species']);

			if (!MonsterSpecies::isValid($species))
				throw new \RuntimeException(sprintf('Unknown species %s while parsing %s', $species, $uri));

			/** @var Monster|null $monster */
			$monster = $this->manager->getRepository(Monster::class)->findOneBy([
				'name' => $name,
			]);

			if (!$monster) {
				$monster = new Monster($name, $monsterType, $species);

				$this->manager->persist($monster);
			} else {
				$monster
					->setType($monsterType)
					->setSpecies($species);

				$monster->setElements([]);
				$monster->setResistances([]);
				$monster->getAilments()->clear();
				$monster->getLocations()->clear();
			}

			$elements = FextralifeHelper::extractElements($rows->eq(3)->children()->last()->text());
			$monster->setElements($elements);

			$ailments = FextralifeHelper::extractAilments($rows->eq(4)->children()->last()->text());

			foreach ($ailments as $name) {
				$ailment = $this->manager->getRepository(Ailment::class)->findOneBy([
					'name' => $name,
				]);

				if (!$ailment)
					throw new \RuntimeException(sprintf('Could not find ailment %s while parsing %s', $name, $uri));

				$monster->getAilments()->add($ailment);
			}
		}
	}