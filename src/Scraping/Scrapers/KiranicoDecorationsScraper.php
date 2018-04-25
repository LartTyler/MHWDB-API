<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Decoration;
	use App\Entity\Skill;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\KiranicoConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Type;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoDecorationsScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * KiranicoDecorationsScraper constructor.
		 *
		 * @param KiranicoConfiguration $configuration
		 * @param ObjectManager         $manager
		 */
		public function __construct(KiranicoConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::DECORATIONS);

			$this->manager = $manager;
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$uri = $this->configuration->getBaseUri()->withPath('/decoration');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container table tr');
			$count = $crawler->count();

			$this->progressBar->append($count);

			for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
				$node = $crawler->eq($i);

				// This should skip the header row
				if ($node->children()->filter('th')->count()) {
					$this->progressBar->advance();

					continue;
				}

				$this->process($node->children()->filter('td')->first());

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

			$name = trim($children->first()->text());
			$slot = (int)substr($name, strrpos($name, ' ') + 1);

			$subtext = trim($children->last()->text());

			preg_match('/Rare (\d+)/', $subtext, $matches);

			if (sizeof($matches) < 2)
				throw new \RuntimeException('Could not determine rarity for ' . $name);

			$rarity = (int)$matches[1];

			/** @var Decoration|null $deco */
			$deco = $this->manager->getRepository('App:Decoration')->findOneBy([
				'name' => $name,
			]);

			if (!$deco) {
				$deco = new Decoration($name, $slot, $rarity);

				$this->manager->persist($deco);
			} else
				$deco
					->setSlot($slot)
					->setRarity($rarity);

			preg_match('/· (.+) ·/', $subtext, $matches);

			if (sizeof($matches) < 2)
				throw new \RuntimeException('Could not determine effect name for ' . $name);

			/** @var Skill|null $skill */
			$skill = $this->manager->getRepository('App:Skill')->findOneBy([
				'name' => trim($matches[1]),
			]);

			if (!$skill)
				throw new \RuntimeException('No skill found named ' . trim($matches[1]) . '(for ' . $name .
					' decoration)');

			$deco->getSkills()->clear();
			$deco->getSkills()->add($skill->getRank(1));

			// DEPRECATED This line preserves BC for < 1.9.0 and will be removed in the future
			$deco->setSkill($skill);
		}
	}