<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use App\Scraper\SubtypeAwareScraperInterface;
	use App\Utility\StringUtil;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoWeaponsScraper extends AbstractScraper implements SubtypeAwareScraperInterface {
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
			WeaponType::LIGHT_BOWGUN => '/light-bowgun',
			WeaponType::HEAVY_BOWGUN => '/heavy-bowgun',
			WeaponType::BOW => '/bow',
		];

		private const SHARPNESS_NODES = [
			Attribute::SHARP_RED,
			Attribute::SHARP_ORANAGE,
			Attribute::SHARP_YELLOW,
			Attribute::SHARP_GREEN,
			Attribute::SHARP_BLUE,
			Attribute::SHARP_WHITE,
		];

		private const ATTRIBUTE_MATCHERS = [
			'/(\d+) (Fire|Water|Ice|Thunder|Dragon|Blast|Poison|Paralysis|Sleep)(\))?/' => [self::class, 'parseElemDamageAttribute'],
			'/((?:Sever|Speed|Element|Health|Stamina|Blunt) Boost)/' => Attribute::IG_BOOST_TYPE,
			'/Affinity +?(-?\d+%?)/' => Attribute::AFFINITY,
			'/((?:Normal|Wide|Long) Lv\d+)/' => Attribute::GL_SHELLING_TYPE,
			'/((?:Poison|Para|Dragon|Exhaust) Phial \d+)/' => Attribute::PHIAL_TYPE,
			'/(?:Power)?((?:Power|Element|Impact) Phial)/' => Attribute::PHIAL_TYPE,
			'/(None|Low|Average|High) Dev./' => Attribute::DEVIATION,
			'/(Wyvern(?:blast|snipe|heart))/' => Attribute::SPECIAL_AMMO,
		];

		private const COATING_TRANSLATIONS = [
			'Cls' => 'Close Range',
			'Pow' => 'Power',
			'Par' => 'Paralysis',
			'Poi' => 'Poison',
			'Sle' => 'Sleep',
			'Bla' => 'Blast',
		];

		private const CAPACITY_TRANSLATIONS = [
			'Nrm' => 'normal',
			'Prc' => 'pierce',
			'Spr' => 'spread',
			'Sti' => 'sticky',
			'Clu' => 'cluster',
			'Rec' => 'recover',
			'Poi' => 'poison',
			'Par' => 'paralysis',
			'Sle' => 'sleep',
			'Exh' => 'exhaust',
			'Fla' => 'flaming',
			'Wat' => 'water',
			'Fre' => 'freeze',
			'Thn' => 'thunder',
			'Dra' => 'dragon',
			'Sli' => 'slicing',
			'Wyv' => 'wyvern',
			'Dem' => 'demon',
			'Arm' => 'armor',
			'Tra' => 'tranq',
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
		public function scrape(array $subtypes = []): void {
			foreach (self::PATHS as $weaponType => $path) {
				if ($subtypes && !in_array($weaponType, $subtypes))
					continue;

				$uri = $this->target->getBaseUri()->withPath($path);
				$response = $this->target->getHttpClient()->get($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container table tr');

				for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
					$nodes = $crawler->eq($i)->children();

					// Skip table header rows
					if ($nodes->filter('th')->count())
						continue;

					$this->process($nodes, $weaponType);
				}

				// We sleep to avoid hitting the target too fast
				sleep(2);
			}
		}

		/**
		 * @param Crawler $nodes
		 * @param string  $weaponType
		 *
		 * @return void
		 */
		protected function process(Crawler $nodes, string $weaponType): void {
			$name = StringUtil::replaceNumeralRank(trim($nodes->first()->filter('a')->text()));
			$rarity = (int)str_replace('RARE', '', trim($nodes->last()->text()));

			/** @var Weapon|null $weapon */
			$weapon = $this->manager->getRepository('App:Weapon')->findOneBy([
				'name' => $name,
			]);

			if (!$weapon) {
				$weapon = new Weapon($name, $weaponType, $rarity);

				$this->manager->persist($weapon);
			} else
				$weapon->setAttributes([]);

			$weapon->setAttribute(Attribute::ATTACK, (int)trim($nodes->eq(1)->text()));

			if ($attributeDescription = trim($nodes->eq(3)->text())) {
				// This fixes a ton of whitespace between phrases in the element description
				$attributeDescription = trim(preg_replace('/\s+/', ' ', $attributeDescription));

				foreach (self::ATTRIBUTE_MATCHERS as $regex => $attribute) {
					if (!preg_match($regex, $attributeDescription, $matches))
						continue;

					// Throw away the full pattern match, we don't want it
					array_shift($matches);

					if (is_string($attribute))
						$weapon->setAttribute($attribute, is_numeric($matches[0]) ? (int)$matches[0] : $matches[0]);
					else if (is_callable($attribute))
						call_user_func($attribute, $weapon, ...$matches);
					else
						throw new \InvalidArgumentException('Can\'t hand attribute value. Check ' . static::class .
							'::ATTRIBUTE_MATCHERS');
				}
			}

			if ($weaponType === WeaponType::BOW) {
				$coatingNodes = $nodes->eq(4)->filter('span');
				$coatings = [];

				for ($i = 0, $ii = $coatingNodes->count(); $i < $ii; $i ++) {
					$node = $coatingNodes->eq($i);
					$classList = $node->attr('class');

					if (!$classList || stripos($classList, 'text-muted') !== false)
						continue;

					$coating = trim($node->text());

					$coatings[] = self::COATING_TRANSLATIONS[$coating] ?? $coating;
				}

				$weapon->setAttribute(Attribute::COATINGS, $coatings);
			} else if (in_array($weaponType, [WeaponType::LIGHT_BOWGUN, WeaponType::HEAVY_BOWGUN])) {
				$capacityNodes = $nodes->eq(4)->filter('div');
				$capacities = [];

				for ($i = 0, $ii = $capacityNodes->count(); $i < $ii; $i++) {
					$row = trim(preg_replace('/\s+/', ' ', $capacityNodes->eq($i)->text()));
					$data = explode(' ', $row);

					if (sizeof($data) === 0)
						throw new \RuntimeException('Could not properly interpret capacities row: ' . $row);

					$currentType = static::getTranslatedCapacityType(array_shift($data));

					while (($next = array_shift($data)) !== null) {
						if (!is_numeric($next)) {
							$currentType = static::getTranslatedCapacityType($next);

							$capacities[$currentType] = [];

							continue;
						}

						$capacities[$currentType][] = (int)$next;
					}
				}
				
				$positions = array_flip(array_values(self::CAPACITY_TRANSLATIONS));
				
				uksort($capacities, function(string $a, string $b) use ($positions): int {
					return $positions[$a] - $positions[$b];
				});

				$weapon->setAttribute(Attribute::AMMO_CAPACITIES, $capacities);
			} else {
				$sharpnessNodes = $nodes->eq(4)->children();

				if ($sharpnessNodes->count()) {
					$sharpnessNodes = $sharpnessNodes->first()->children();

					foreach (self::SHARPNESS_NODES as $i => $sharpness) {
						$styles = $sharpnessNodes->eq($i)->attr('style');

						if (!$styles || !preg_match('/width: ?(\d+)px/', $styles, $matches))
							continue;

						$value = (int)$matches[1];

						if ($value === 0)
							break;

						$weapon->setAttribute($sharpness, (int)$matches[1]);
					}
				}
			}

			$slotNodes = $nodes->eq(5)->children()->first()->children();
			$slotCounts = [];

			for ($i = 0, $ii = $slotNodes->count(); $i < $ii; $i++) {
				if (!preg_match('/zmdi-n-(\d+)-square/', $slotNodes->eq($i)->attr('class'), $matches))
					continue;

				$key = (int)$matches[1];

				if (!isset($slotCounts[$key]))
					$slotCounts[$key] = 0;

				++$slotCounts[$key];
			}

			foreach ($slotCounts as $rank => $count)
				$weapon->setAttribute('slotsRank' . $rank, $count);
		}

		/**
		 * @param Weapon      $weapon
		 * @param string      $value
		 * @param string      $element
		 * @param null|string $hiddenChar
		 *
		 * @return void
		 */
		public static function parseElemDamageAttribute(
			Weapon $weapon,
			string $value,
			string $element,
			?string $hiddenChar = null
		): void {
			$weapon
				->setAttribute(Attribute::ELEM_TYPE, $element)
				->setAttribute(Attribute::ELEM_DAMAGE, (int)$value)
				->setAttribute(Attribute::ELEM_HIDDEN, $hiddenChar !== null);
		}

		/**
		 * @param string $type
		 *
		 * @return string
		 */
		protected static function getTranslatedCapacityType(string $type): string {
			return self::CAPACITY_TRANSLATIONS[$type] ?? $type;
		}
	}