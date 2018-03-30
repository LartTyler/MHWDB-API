<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use App\Utility\StringUtil;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoSkillsScraper extends AbstractScraper {
		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var EntityManager
		 */
		protected $manager;

		/**
		 * KiranicoSkillsScraper constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param RegistryInterface    $doctrine
		 */
		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $doctrine) {
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

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container table tr');

			for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
				$node = $crawler->eq($i);

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

			$href = $initialNode->filter('a')->attr('href');

			$uri = $this->target->getBaseUri()->withPath(parse_url($href, PHP_URL_PATH));

			try {
				$response = $this->target->getHttpClient()->get($uri);
			} catch (\Exception $e) {
				sleep(3);

				$response = $this->target->getHttpClient()->get($uri);
			}

			$sections = (new Crawler($response->getBody()->getContents()))->filter('.container .px-2 .card');

			$description = StringUtil::clean($sections->eq(1)->filter('.card-body p.lead')->eq(0)->text());

			if (($pos = strpos($description, 'This skill can be obtained')) !== false)
				$description = trim(substr($description, 0, $pos));

			// Typo correction
			$description = str_replace([
				'protection form large',
			], [
				'protection from large',
			], $description);

			$skill->setDescription($description);
		}
	}