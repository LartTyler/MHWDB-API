<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Game\ArmorRank;
	use App\Game\Attribute;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\KiranicoConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\Scrapers\Helpers\KiranicoArmorHelper;
	use App\Scraping\Scrapers\Helpers\KiranicoHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoArmorScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * @var ArmorSet[]
		 */
		protected $setCache = [];

		/**
		 * KiranicoArmorScraper constructor.
		 *
		 * @param KiranicoConfiguration $configuration
		 * @param ObjectManager         $manager
		 */
		public function __construct(KiranicoConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::ARMOR);

			$this->manager = $manager;
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$uri = $this->configuration->getBaseUri()->withPath('/armor');
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .tab-content .card');
			$count = $crawler->count();

			$this->progressBar->append($count);

			$currentRank = ArmorRank::LOW;

			for ($i = 0; $i < $count; $i++) {
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

				$this->progressBar->advance();
			}

			$this->manager->flush();
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
			$uri = $this->configuration->getBaseUri()->withPath($path);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$crawler = (new Crawler($response->getBody()->getContents()))->filter('.container .col-lg-9.px-2')
				->children();

			$rawName = $crawler->filter('.card .media h1[itemprop=name]')->text();

			/**
			 * @var string $name
			 * @var string $armorType
			 */
			list($name, $armorType) = KiranicoArmorHelper::parseArmorName($rawName);

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
				->setAttribute(Attribute::DEFENSE, (int)strtok(trim($infoNodes->eq(0)->filter('.lead')->text()), ' '))
				->addAttributes(KiranicoHelper::getSlots($infoNodes->eq(1)->filter('.zmdi')));

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