<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Armor;
	use App\Entity\SkillRank;
	use App\Game\ArmorType;
	use App\Game\Attribute;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\CssSelector\Node\AttributeNode;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoArmorScraper extends AbstractScraper {
		private const HIGH_RANK_SUFFIX_TRANSLATIONS = [
			'Alpha' => 'α',
			'Beta' => 'β',
		];

		private const ARMOR_TYPE_PHRASES = [
			// -- Standard --
			'Headgear' => ArmorType::HEAD,
			'Headpiece' => ArmorType::HEAD,
			'Helm' => ArmorType::HEAD,
			'Hood' => ArmorType::HEAD,
			'Vertex' => ArmorType::HEAD,
			'Goggles' => ArmorType::HEAD,
			'Hat' => ArmorType::HEAD,
			'Mask' => ArmorType::HEAD,
			'Brain' => ArmorType::HEAD,
			'Lobos' => ArmorType::HEAD,
			'Crown' => ArmorType::HEAD,
			'Glare' => ArmorType::HEAD,
			'Horn' => ArmorType::HEAD,
			'Circlet' => ArmorType::HEAD,
			'Gorget' => ArmorType::HEAD,
			'Spectacles' => ArmorType::HEAD,
			'Eyepatch' => ArmorType::HEAD,
			'Mail' => ArmorType::CHEST,
			'Vest' => ArmorType::CHEST,
			'Thorax' => ArmorType::CHEST,
			'Muscle' => ArmorType::CHEST,
			'Suit' => ArmorType::CHEST,
			'Jacket' => ArmorType::CHEST,
			'Hide' => ArmorType::CHEST,
			'Cista' => ArmorType::CHEST,
			'Armor' => ArmorType::CHEST,
			'Gloves' => ArmorType::GLOVES,
			'Vambraces' => ArmorType::GLOVES,
			'Guards' => ArmorType::GLOVES,
			'Braces' => ArmorType::GLOVES,
			'Brachia' => ArmorType::GLOVES,
			'Grip' => ArmorType::GLOVES,
			'Longarms' => ArmorType::GLOVES,
			'Claws' => ArmorType::GLOVES,
			'Belt' => ArmorType::WAIST,
			'Coil' => ArmorType::WAIST,
			'Elytra' => ArmorType::WAIST,
			'Bowels' => ArmorType::WAIST,
			'Hoop' => ArmorType::WAIST,
			'Spine' => ArmorType::WAIST,
			'Cocoon' => ArmorType::WAIST,
			'Trousers' => ArmorType::LEGS,
			'Greaves' => ArmorType::LEGS,
			'Boots' => ArmorType::LEGS,
			'Crura' => ArmorType::LEGS,
			'Heel' => ArmorType::LEGS,
			'Heels' => ArmorType::LEGS,
			'Leg Guards' => ArmorType::LEGS,
			'Spurs' => ArmorType::LEGS,
			'Crus' => ArmorType::LEGS,
			'Pants' => ArmorType::LEGS,

			// -- Special --
			'Faux Felyne' => ArmorType::HEAD,
		];

		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var EntityManagerInterface
		 */
		protected $manager;

		/**
		 * KiranicoArmorScraper constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param RegistryInterface    $registry
		 */
		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $registry) {
			parent::__construct($target, ScraperType::ARMOR);

			$this->manager = $registry->getManager();
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(): void {
			$uri = $this->target->getBaseUri()->withPath('/armor');
			$response = $this->target->getHttpClient()->get($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .tab-content .card');

			for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
				$setPieceNodes = $crawler->eq($i)->filter('.card-body table')->eq(0)->filter('tr');

				for ($j = 0, $jj = $setPieceNodes->count(); $j < $jj; $j++) {
					$link = $setPieceNodes->eq($j)->filter('a')->attr('href');

					$this->process(parse_url($link, PHP_URL_PATH));
				}
			}
		}

		/**
		 * @param string $path
		 *
		 * @throws \Http\Client\Exception
		 */
		protected function process(string $path) {
			$uri = $this->target->getBaseUri()->withPath($path);
			$response = $this->target->getHttpClient()->get($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);
			
			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .col-lg-9.px-2')
				->children();

			$rawName = $crawler->filter('.card .media h1[itemprop=name]')->text();

			/**
			 * @var string $name
			 * @var string $armorType
			 */
			list($name, $armorType) = $this->parseArmorName($rawName);

			/** @var Armor|null $armor */
			$armor = $this->manager->getRepository('App:Armor')->findOneBy([
				'name' => $name,
			]);

			if (!$armor) {
				$armor = new Armor($name, $armorType);

				$this->manager->persist($armor);
			} else {
				$armor->setAttributes([]);
				$armor->getSkills()->clear();
			}

			$infoNodes = $crawler->filter('.card')->eq(1)->filter('.card-footer .p-3');

			$armor
				->setAttribute(Attribute::DEFENSE, (int)strtok(trim($infoNodes->eq(0)->filter('.lead')->text()), ' '));

			$slotNodes = $infoNodes->eq(1)->filter('.zmdi');
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
				$armor->setAttribute('slotsRank' . $rank, $count);

			$attributeNodes = $crawler->filter('.row.no-gutters')->children();

			$elemResists = $attributeNodes->eq(0)->filter('.card-body table tr');

			for ($i = 0, $ii = $elemResists->count(); $i < $ii; $i++) {
				$children = $elemResists->eq($i)->children();
				$elemText = trim($children->first()->text());

				$elem = trim(substr($elemText, strrpos($elemText, ' ') + 1));
				$value = trim($children->last()->text());

				if (strpos($value, '+') === 0)
					$value = substr($value, 1);

				$value = (int)$value;

				if ($value === 0)
					continue;

				$armor->setAttribute('resist' . $elem, $value);
			}

			$skills = $attributeNodes->eq(1)->filter('.card-body table tr');

			for ($i = 0, $ii = $skills->count(); $i < $ii; $i++) {
				$children = $skills->eq($i)->children();

				$skillName = trim($children->first()->text());

				$skill = $this->manager->getRepository('App:Skill')->findOneBy([
					'name' => $skillName,
				]);

				if (!$skill)
					throw new \RuntimeException($skillName . ' is not a known skill');

				$skillRank = trim($children->last()->text());

				if (strpos($skillRank, '+') === 0)
					$skillRank = substr($skillRank, 1);

				$rank = $skill->getRank((int)$skillRank);

				if (!$rank)
					throw new \RuntimeException($skillName . ' has no rank labelled "' . $skillRank . '"');

				$armor->getSkills()->add($rank);
			}

			// We sleep to avoid hitting the target too frequently
			sleep(1);
		}

		/**
		 * @param string $rawName
		 *
		 * @return array
		 */
		protected function parseArmorName(string $rawName): array {
			/**
			 * Does a few things:
			 *
			 * 1. Replaces any consecutive whitespace characters with a single space
			 * 2. Corrects any typos present on the scrape target
			 */
			$cleanedName = str_replace([
				'Apha',
				'Barchia',
			], [
				'Alpha',
				'Brachia',
			], preg_replace('/\s+/', ' ', $rawName));

			$parts = array_filter(array_map(function(string $part): string {
				return trim($part);
			}, explode(' ', $cleanedName)));

			$partCount = sizeof($parts);

			if (isset(KiranicoArmorScraper::HIGH_RANK_SUFFIX_TRANSLATIONS[$parts[$partCount - 1]])) {
				$rank = KiranicoArmorScraper::HIGH_RANK_SUFFIX_TRANSLATIONS[array_pop($parts)];

				--$partCount;
			} else
				$rank = '';

			$armorType = null;
			$partOffsetMax = $partCount - 1;

			foreach (KiranicoArmorScraper::ARMOR_TYPE_PHRASES as $phrase => $type) {
				$consumeCount = substr_count($phrase, ' ');

				// If we'd need to consume more of the array than there are pieces, this can't possibly be our match
				if ($consumeCount > $partCount)
					continue;

				$candidate = implode(' ', array_slice($parts, $partOffsetMax - $consumeCount));

				if ($candidate === $phrase) {
					$armorType = $type;

					break;
				}
			}

			if ($armorType === null)
				throw new \RuntimeException('Could not determine armor type from name: ' . $rawName);

			return [
				trim(implode(' ', $parts) . ' ' . $rank),
				$armorType,
			];
		}
	}