<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Skill;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoSkillsScraper extends AbstractScraper {
		/**
		 * KiranicoSkillsScraper constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 */
		public function __construct(KiranicoScrapeTarget $target) {
			parent::__construct($target, ScraperType::SKILLS);
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return Skill[]|\Generator
		 */
		public function scrape(): \Generator {
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
					yield $this->buildSkill($node, $rowCount - 1);
			}

			return $skills;
		}

		/**
		 * @param Crawler $initialNode
		 * @param int     $rankCount
		 *
		 * @return Skill
		 */
		protected function buildSkill(Crawler $initialNode, int $rankCount): Skill {
			$ranks = [];

			for ($i = 0; $i < $rankCount; $i++)
				$ranks[] = $initialNode->nextAll()->eq($i)->text();

			return new Skill($initialNode->children()->text(), $ranks);
		}
	}