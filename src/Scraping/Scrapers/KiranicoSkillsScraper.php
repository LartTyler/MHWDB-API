<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\KiranicoConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\KiranicoSkillHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoSkillsScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * @var array
		 */
		protected $missingSkills;

		/**
		 * @var Skill[]
		 */
		protected $skillCache = [];

		/**
		 * KiranicoSkillsScraper constructor.
		 *
		 * @param KiranicoConfiguration $configuration
		 * @param ObjectManager         $manager
		 * @param string                $missingSkillsFile
		 */
		public function __construct(
			KiranicoConfiguration $configuration,
			ObjectManager $manager,
			string $missingSkillsFile
		) {
			parent::__construct($configuration, Type::SKILLS);

			$this->manager = $manager;
			$this->missingSkills = json_decode(file_get_contents($missingSkillsFile), true);
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$uri = $this->configuration->getBaseUri()->withPath('/skill');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \Exception('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container table tr');
			$count = $crawler->count();

			$this->progressBar->append($count + sizeof($this->missingSkills));

			for ($i = 0; $i < $count; $i++) {
				$node = $crawler->eq($i);

				if ($rowCount = $node->children()->first()->attr('rowspan'))
					// For some reason, rows on Kiranico are level count + 1 for rowspan
					$this->process($node, $rowCount - 1);

				$this->progressBar->advance();
			}

			foreach ($this->missingSkills as $name => $skillData) {
				if (isset($this->skillCache[$name]))
					continue;

				$skill = $this->manager->getRepository('App:Skill')->findOneBy([
					'name' => $name,
				]);

				if ($skill)
					continue;

				$skill = new Skill($name);
				$this->manager->persist($skill);

				$skill->setDescription($skillData['description']);

				foreach ($skillData['ranks'] as $rankData) {
					$rank = new SkillRank($skill, $rankData['level'], $rankData['description']);

					$skill->getRanks()->add($rank);
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param Crawler $initialNode
		 * @param int     $rankCount
		 *
		 * @return void
		 */
		protected function process(Crawler $initialNode, int $rankCount): void {
			$skillName = trim($initialNode->children()->text());

			/** @var Skill|null $skill */
			$skill = $this->manager->getRepository('App:Skill')->findOneBy([
				'name' => $skillName,
			]);

			if (!$skill) {
				$skill = new Skill($skillName);

				$this->manager->persist($skill);
			}

			$this->skillCache[$skillName] = $skill;

			for ($i = 0; $i < $rankCount; $i++) {
				$description = trim($initialNode->nextAll()->eq($i)->children()->last()->text());
				$rank = $skill->getRank($i + 1);

				if (!$rank) {
					$rank = new SkillRank($skill, $i + 1, $description);

					$skill->getRanks()->add($rank);
				}

				$rank->setModifiers(KiranicoSkillHelper::parseRankDescriptions($description));
			}

			$href = $initialNode->filter('a')->attr('href');

			$uri = $this->configuration->getBaseUri()->withPath(parse_url($href, PHP_URL_PATH));
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

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