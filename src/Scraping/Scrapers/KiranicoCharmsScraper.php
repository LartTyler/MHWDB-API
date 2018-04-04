<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Charm;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configuration;
	use App\Scraping\Configurations\KiranicoConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoCharmsScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * KiranicoCharmsScraper constructor.
		 *
		 * @param KiranicoConfiguration $configuration
		 * @param ObjectManager         $manager
		 */
		public function __construct(KiranicoConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::CHARMS);

			$this->manager = $manager;
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$uri = $this->configuration->getBaseUri()->withPath('/charm');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container table tr');
			$count = $crawler->count();

			$this->progressBar->append($count);

			for ($i = 0; $i < $count; $i++) {
				$this->process($crawler->eq($i));

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param Crawler $node
		 *
		 * @return void
		 */
		protected function process(Crawler $node): void {
			$children = $node->children();
			$name = StringUtil::replaceNumeralRank(trim($children->first()->text()));

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