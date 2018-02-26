<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Charm;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use App\Utility\StringUtil;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoCharmsScraper extends AbstractScraper {
		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var EntityManager
		 */
		protected $manager;

		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $registry) {
			parent::__construct($target, ScraperType::CHARMS);

			$this->manager = $registry->getManager();
		}

		/**
		 * @return void
		 */
		public function scrape(): void {
			$uri = $this->target->getBaseUri()->withPath('/charm');
			$response = $this->target->getHttpClient()->get($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container table tr');

			for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++)
				$this->process($crawler->eq($i));
		}

		/**
		 * @param Crawler $node
		 *
		 * @return void
		 */
		protected function process(Crawler $node): void {
			$children = $node->children();
			$name = trim($children->first()->text());

			/** @var Charm|null $charm */
			$charm = $this->manager->getRepository('App:Charm')->findOneBy([
				'name' => $name,
			]);

			if (!$charm) {
				$charm = new Charm($name);

				$this->manager->persist($charm);
			} else
				$charm->getSkills()->clear();

			$skillNodes = $children->eq(1)->children()->filter('div');

			for ($i = 0, $ii = $skillNodes->count(); $i < $ii; $i++) {
				$description = trim($skillNodes->eq($i)->text());
				$pos = strrpos($description, ' ');

				$skillName = trim(substr($description, 0, $pos));

				$skill = $this->manager->getRepository('App:Skill')->findOneBy([
					'name' => $skillName,
				]);

				if (!$skill)
					throw new \RuntimeException(sprintf('"%s" is not a recognized skill name', $skillName));

				$skillRank = trim(substr($description, $pos + 1));
				$rank = $skill->getRank((int)$skillRank);

				if (!$rank)
					throw new \RuntimeException(sprintf('"%s" is not a recognized rank for %s', $skillRank, $skillName));

				$charm->getSkills()->add($rank);
			}
		}
	}