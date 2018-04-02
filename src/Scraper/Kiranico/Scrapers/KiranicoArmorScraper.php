<?php
	namespace App\Scraper\Kiranico\Scrapers;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\SkillRank;
	use App\Game\ArmorRank;
	use App\Game\ArmorType;
	use App\Game\Attribute;
	use App\Scraper\AbstractScraper;
	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\ScraperType;
	use App\Utility\StringUtil;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\CssSelector\Node\AttributeNode;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoArmorScraper extends AbstractScraper {
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
			'Shades' => ArmorType::HEAD,
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
		 * @var ArmorSet[]
		 */
		private $setCache = [];

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
			$currentRank = ArmorRank::LOW;

			for ($i = 0, $ii = $crawler->count(); $i < $ii; $i++) {
				$setNode = $crawler->eq($i);

				$setName = StringUtil::clean($setNode->filter('.card-header')->text());
				$setName = trim(str_replace('Set', '', $setName));

				$set = $this->getArmorSet($setName);

				if (!$set) {
					if (strpos($setName, 'Alpha') || strpos($setName, 'Beta'))
						$rank = ArmorRank::HIGH;
					else
						$rank = ArmorRank::LOW;

					$this->setCache[$setName] = $set = new ArmorSet($setName, $rank);

					$this->manager->persist($set);
				}

				$setPieceNodes = $setNode->filter('.card-body table')->eq(0)->filter('tr');

				for ($j = 0, $jj = $setPieceNodes->count(); $j < $jj; $j++) {
					$link = $setPieceNodes->eq($j)->filter('a')->attr('href');

					if (stripos($link, 'Alpha'))
						$currentRank = ArmorRank::HIGH;

					$this->process(parse_url($link, PHP_URL_PATH), $currentRank, $set);
				}
			}
		}

		/**
		 * @param string   $path
		 * @param string   $rank
		 * @param ArmorSet $armorSet
		 *
		 * @return void
		 * @throws \Http\Client\Exception
		 */
		protected function process(string $path, string $rank, ArmorSet $armorSet): void {
			$uri = $this->target->getBaseUri()->withPath($path);

			try {
				$response = $this->target->getHttpClient()->get($uri);
			} catch (\Exception $e) {
				sleep(3);

				$response = $this->target->getHttpClient()->get($uri);
			}

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

			/**
			 * 0 = Defense
			 * 1 = Slots
			 * 2 = Price
			 * 3 = Gender
			 * 4 = Rarity
			 */
			$infoNodes = $crawler->filter('.card')->eq(1)->filter('.card-footer .p-3');

			$rarity = (int)StringUtil::clean($infoNodes->eq(4)->filter('.lead')->text());

			if (!$armor) {
				$armor = new Armor($name, $armorType, $rank, $rarity);

				$this->manager->persist($armor);
			} else {
				$armor
					->setRarity($rarity)
					->setAttributes([]);

				$armor->getSkills()->clear();
			}

			$armor->setArmorSet($armorSet);

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

			$genderNodes = $infoNodes->eq(3)->filter('.zmdi:not(.text-dark)');

			if ($genderNodes->count() < 2) {
				preg_match('/zmdi-(female|male)/', $genderNodes->first()->attr('class'), $matches);

				if (sizeof($matches) >= 2)
					$armor->setAttribute(Attribute::REQUIRED_GENDER, $matches[1]);
			}

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

			if (in_array($parts[$partCount - 1], ['Alpha', 'Beta'])) {
				$rank = array_pop($parts);

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

		/**
		 * @param string $name
		 *
		 * @return ArmorSet|null
		 */
		protected function getArmorSet(string $name): ?ArmorSet {
			if (isset($this->setCache[$name]))
				return $this->setCache[$name];

			$set = $this->manager->getRepository('App:ArmorSet')->findOneBy([
				'name' => $name,
			]);

			return $this->setCache[$name] = $set;
		}
	}