<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Game\Attribute;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use Doctrine\Bundle\DoctrineBundle\Registry;
	use Doctrine\ORM\EntityManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoSkillsScraper extends AbstractScraper {
		/**
		 * @var EntityManager
		 */
		protected $manager;

		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * KiranicoSkillsScraper constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param Registry             $doctrine
		 */
		public function __construct(KiranicoScrapeTarget $target, Registry $doctrine) {
			parent::__construct($target, ScraperType::SKILLS);

			$this->manager = $doctrine->getManager();
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return void
		 */
		public function scrape(): void {
			$uri = $this->target->getBaseUri()->withPath('/skill');
			$response = $this->target->getHttpClient()->get($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \Exception('Could not retrieve ' . $uri);

			$crawler = new Crawler($response->getBody()->getContents());
			$skillsCrawler = $crawler->filter('.container table tr');

			$skills = [];

			for ($i = 0, $ii = $skillsCrawler->count(); $i < $ii; $i++) {
				$node = $skillsCrawler->eq($i);

				if ($rowCount = $node->children()->first()->attr('rowspan'))
					// For some reason, rows on Kiranico are level count + 1 for rowspan
					$this->process($node, $rowCount - 1);
			}
		}

		/**
		 * @param Crawler $initialNode
		 * @param int     $rankCount
		 *
		 * @return void
		 */
		protected function process(Crawler $initialNode, int $rankCount): void {
			$skillName = trim($initialNode->children()->text());

			$skill = $this->manager->getRepository('App:Skill')->findOneBy([
				'name' => $skillName,
			]);

			if (!$skill) {
				$skill = new Skill($skillName);

				$this->manager->persist($skill);
			}

			for ($i = 0; $i < $rankCount; $i++) {
				$description = trim($initialNode->nextAll()->eq($i)->children()->last()->text());
				$rank = $skill->getRank($i + 1);

				if (!$rank) {
					$rank = new SkillRank($skill, $i + 1, $description);

					$skill->getRanks()->add($rank);
				}

				$rank->setModifiers($this->target->parseRankDescriptions($description));
			}
		}
	}