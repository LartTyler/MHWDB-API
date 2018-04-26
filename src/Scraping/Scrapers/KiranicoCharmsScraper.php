<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\CharmRankCraftingInfo;
	use App\Entity\CraftingMaterialCost;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\KiranicoConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\KiranicoHelper;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\KiranicoCharmHelper;
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
		 * @var Charm[]
		 */
		protected $charmCache = [];

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
			/**
			 * 0 = Name and level
			 * 1 = Skills
			 * 2 = Material Cost
			 */
			$cells = $node->children();

			$fullName = StringUtil::replaceNumeralRank(StringUtil::clean($cells->first()->text()));

			/**
			 * @var string $name
			 * @var int    $level
			 */
			[$name, $level] = KiranicoHelper::splitNameAndLevel($fullName);

			$charm = $this->findCharm($name);

			if (!$charm) {
				$charm = new Charm($name);
				$this->charmCache[$name] = $charm;

				$this->manager->persist($charm);
			}

			$charmRank = $charm->getRank($level);

			if (!$charmRank) {
				$charmRank = new CharmRank($charm, $fullName, $level);

				$charm->getRanks()->add($charmRank);
			} else
				$charmRank->getSkills()->clear();

			$skillNodes = $cells->eq(1)->children()->filter('div');

			for ($i = 0, $ii = $skillNodes->count(); $i < $ii; $i++) {
				/**
				 * @var string $skillName
				 * @var int    $skillLevel
				 */
				[$skillName, $skillLevel] = KiranicoHelper::splitNameAndLevel(
					StringUtil::clean($skillNodes->eq($i)->text())
				);

				$skill = $this->manager->getRepository('App:Skill')->findOneBy([
					'name' => $skillName,
				]);

				if (!$skill)
					throw new \RuntimeException('[Charms] No skill found named ' . $skillName);

				$skillRank = $skill->getRank($skillLevel);

				if (!$skillRank)
					throw new \RuntimeException(sprintf('[Charms] Level %d does not exist on %s', $skillName,
						$skillLevel));

				$charmRank->getSkills()->add($skillRank);
			}

			$materialNodes = $cells->eq(2)->filter('div');

			if ($crafting = $charmRank->getCrafting())
				$crafting->getMaterials()->clear();
			else
				$charmRank->setCrafting($crafting = new CharmRankCraftingInfo($charmRank->getLevel() === 1));

			for ($i = 0, $ii = $materialNodes->count(); $i < $ii; $i++) {
				$text = StringUtil::clean($materialNodes->eq($i)->text());

				preg_match('/^(.+) x(\d+)$/', $text, $matches);

				if (sizeof($matches) < 3)
					throw new \RuntimeException('[Charms] Could not parse material cost: ' . $text);

				$item = $this->manager->getRepository('App:Item')->findOneBy([
					'name' => $matches[1],
				]);

				if (!$item)
					throw new \RuntimeException('[Charms] Could not find item named ' . $matches[1]);

				$crafting->getMaterials()->add(new CraftingMaterialCost($item, (int)$matches[2]));
			}
		}

		/**
		 * @param string $name
		 *
		 * @return Charm|null
		 */
		protected function findCharm(string $name): ?Charm {
			if (isset($this->charmCache[$name]))
				return $this->charmCache[$name];

			/** @var Charm|null $charm */
			$charm = $this->manager->getRepository('App:Charm')->findOneBy([
				'name' => $name,
			]);

			return $this->charmCache[$name] = $charm;
		}
	}