<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use App\Utility\StringUtil;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoWeaponsScraper extends AbstractScraper {
		private const PATHS = [
			WeaponType::GREAT_SWORD => '/great-sword',
			WeaponType::LONG_SWORD => '/long-sword',
			WeaponType::SWORD_AND_SHIELD => '/sword',
			WeaponType::DUAL_BLADES => '/dual-blades',
			WeaponType::HAMMER => '/hammer',
			WeaponType::HUNTING_HORN => '/hunting-horn',
			WeaponType::LANCE => '/lance',
			WeaponType::GUNLANCE => '/gunlance',
			WeaponType::SWITCH_AXE => '/switch-axe',
			WeaponType::CHARGE_BLADE => '/charge-blade',
			WeaponType::INSECT_GLAIVE => '/insect-glaive',
			// WeaponType::LIGHT_BOWGUN => '/light-bowgun',
			// WeaponType::HEAVY_BOWGUN => '/heavy-bowgun',
			// WeaponType::BOW => '/bow',
		];

		private const SHARPNESS_NODES = [
			Attribute::SHARP_RED,
			Attribute::SHARP_ORANAGE,
			Attribute::SHARP_YELLOW,
			Attribute::SHARP_GREEN,
			Attribute::SHARP_BLUE,
			Attribute::SHARP_WHITE,
		];

		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var EntityManager
		 */
		protected $manager;

		/**
		 * KiranicoWeaponsScraper constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param RegistryInterface    $registry
		 */
		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $registry) {
			parent::__construct($target, ScraperType::WEAPONS);

			$this->manager = $registry->getManager();
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(): void {
			foreach (self::PATHS as $weaponType => $path) {
				$uri = $this->target->getBaseUri()->withPath($path);
				$response = $this->target->getHttpClient()->get($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container table tr');

				for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
					$nodes = $crawler->eq($i)->children();

					$this->process($nodes, $weaponType);
				}
			}
		}

		/**
		 * @param Crawler $nodes
		 * @param string  $weaponType
		 *
		 * @return void
		 */
		protected function process(Crawler $nodes, string $weaponType): void {
			$name = StringUtil::replaceNumeralRank(trim($nodes->first()->children()->filter('a')->text()));
			$rarity = (int)str_replace('RARE', '', trim($nodes->last()->text()));

			$weapon = new Weapon($name, $weaponType, $rarity);

			$weapon->setAttribute(Attribute::ATTACK, (int)trim($nodes->eq(1)->text()));

			if ($elemDescription = trim($nodes->eq(3)->text())) {
				if (strpos($elemDescription, '(') === 0) {
					$weapon->setAttribute(Attribute::ELEM_HIDDEN, true);

					$elemDescription = substr($elemDescription, 1, strlen($elemDescription) - 2);
				}

				$weapon
					->setAttribute(Attribute::ELEM_DAMAGE, (int)strtok($elemDescription, ' '))
					->setAttribute(Attribute::ELEM_TYPE, strtok(''));
			}

			$sharpnessNodes = $nodes->eq(4)->filter('div div');

			foreach (self::SHARPNESS_NODES as $index => $sharpness) {
				$styles = $sharpnessNodes->eq($index)->attr('style');

				if (!$styles || !preg_match('/width: ?(\d+)px/', $styles, $matches))
					continue;

				$weapon->setAttribute($sharpness, (int)$matches[1]);
			}

			$slotNodes = $nodes->eq(5)->filter('small i');
			$slotCounts = [];

			for ($j = 0, $jj = $slotNodes->count(); $j < $jj; $j++) {
				if (!preg_match('/zmdi-n-(\d+)-square/', $slotNodes->eq($j)->attr('class'), $matches))
					continue;

				$key = (int)$matches[1];

				if (!isset($slotCounts[$key]))
					$slotCounts[$key] = 0;

				++$slotCounts[$key];
			}

			foreach ($slotCounts as $rank => $count)
				$weapon->setAttribute('slotsRank' . $rank, $count);
		}
	}