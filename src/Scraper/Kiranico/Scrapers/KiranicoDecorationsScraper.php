<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Decoration;
	use App\Entity\Skill;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoDecorationsScraper extends AbstractScraper {
		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * KiranicoDecorationsScraper constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param RegistryInterface    $registry
		 */
		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $registry) {
			parent::__construct($target, ScraperType::DECORATIONS);

			$this->manager = $registry->getManager();
		}

		/**
		 * @return void
		 * @throws \Http\Client\Exception
		 */
		public function scrape(): void {
			$uri = $this->target->getBaseUri()->withPath('/decoration');
			$result = $this->target->getHttpClient()->get($uri);

			if ($result->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);
			
			$crawler = (new Crawler($result->getBody()->getContents()))->filter('.container table tr');

			for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
				$node = $crawler->eq($i);

				// This should skip the header row
				if ($node->children()->filter('th')->count())
					continue;

				$this->process($node->children()->filter('td')->first());
			}
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

			$deco->setSkill($skill);
		}
	}