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
		 */
		public function scrape(): array {
			$uri = $this->target->getBaseUri()->withPath('/skill');
			$response = $this->target->getHttpClient()->get($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \Exception('Could not retrieve ' . $uri);

			$crawler = new Crawler($response->getBody()->getContents());
			$skillsCrawler = $crawler->filter('.container table tbody tr');

			$skills = [];

			for ($i = 0, $ii = $skillsCrawler->count(); $i < $ii; $i++) {
				$node = $skillsCrawler->eq($i);

				if ($node->children()->first()->attr('rowspan'))
					$skills[] = $this->buildSkill($node);
			}

			return $skills;
		}

		/**
		 * @param Crawler $initialNode
		 *
		 * @return Skill
		 */
		protected function buildSkill(Crawler $initialNode): Skill {
			$ranks = [];

			do {
				$current = $initialNode->nextAll()->first();

				$ranks[] = $current->children()->last()->text();
			} while (!$current->children()->first()->attr('rowspan'));

			return new Skill($initialNode->children()->text(), $ranks);
		}
	}