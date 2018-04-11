<?php
	namespace App\Scraping\Scrapers;

	use App\Game\Attribute;
	use App\Game\Elderseal;
	use App\Game\WeaponType;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\MHWikiaConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\ScraperInterface;
	use App\Scraping\Scrapers\Helpers\EldersealData;
	use App\Scraping\Scrapers\Helpers\MHWikiaHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class MHWikiaEldersealScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * @var EldersealData
		 */
		protected $missingValues;

		/**
		 * MHWikiaEldersealScraper constructor.
		 *
		 * @param MHWikiaConfiguration $configuration
		 * @param ObjectManager        $manager
		 * @param EldersealData        $missingValues
		 */
		public function __construct(
			MHWikiaConfiguration $configuration,
			ObjectManager $manager,
			EldersealData $missingValues
		) {
			parent::__construct($configuration, Type::ELDERSEAL);

			$this->manager = $manager;
			$this->missingValues = $missingValues;
		}

		/**
		 * @param array $context
		 *
		 * @return void
		 */
		public function scrape(array $context = []): void {
			$subtypes = $context[ScraperInterface::CONTEXT_SUBTYPES] ?? [];

			$this->progressBar->append($subtypes ? sizeof($subtypes) : sizeof(MHWikiaHelper::WEAPON_TREE_PATHS));

			foreach (MHWikiaHelper::WEAPON_TREE_PATHS as $weaponType => $path) {
				if ($subtypes && !in_array($weaponType, $subtypes))
					continue;

				$extra = $this->missingValues->get($weaponType);

				$uri = $this->configuration->getBaseUri()->withPath($path);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$crawler = (new Crawler($response->getBody()->getContents()))
					->filter('#mw-content-text .wikitable.hover tr');

				$count = $crawler->count();

				$this->progressBar->append($count + sizeof($extra));

				for ($i = 0; $i < $count; $i++) {
					$row = $crawler->eq($i);

					if ($row->children()->first()->nodeName() === 'th') {
						$this->progressBar->advance();

						continue;
					}

					$this->process($row, $weaponType);

					$this->progressBar->advance();
				}

				foreach ($extra as $weaponName => $eldersealValue) {
					$weapon = $this->manager->getRepository('App:Weapon')->findOneBy([
						'name' => $weaponName,
						'type' => $weaponType,
					]);

					if (!$weapon)
						throw new \RuntimeException('Could not find weapon named ' . $weaponName);

					if ($eldersealValue !== null)
						$weapon->setAttribute(Attribute::ELDERSEAL, $eldersealValue);
					else
						$weapon->removeAttribute(Attribute::ELDERSEAL);

					$this->progressBar->advance();
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param Crawler $row
		 * @param string  $weaponType
		 *
		 * @return void
		 */
		protected function process(Crawler $row, string $weaponType): void {
			/**
			 * Row cell indexes are as follows:
			 * 0 = Name
			 * 1 = Attack
			 * 2 = Special (element)
			 * 3 = Sharpness
			 * 4 = Slots
			 * 5 = Affinity
			 * 6 = Elderseal
			 * 7 = Defense
			 */
			$cells = $row->filter('td');

			if ($cells->count() < 7)
				return;

			if (in_array($weaponType, [WeaponType::GREAT_SWORD, WeaponType::LIGHT_BOWGUN, WeaponType::HEAVY_BOWGUN]))
				$weaponName = $cells->eq(0)->filter('a')->last()->text();
			else
				$weaponName = $cells->eq(0)->text();

			$weaponName = str_replace([
				'→'
			], [
				'',
			], $weaponName);

			$weaponName = StringUtil::replaceNumeralRank(StringUtil::clean($weaponName));

			// General typo correction
			$weaponName = str_replace([
				'’',
				'Exterminator\'s',
				'Eraadication',
				'Bazel Myriad',
				'Thunderspike',
				'Chroma',
				'Snowfeltcher',
				'Mentora',
			], [
				'\'',
				'Extermination\'s',
				'Eradication',
				'Bazel Myniad',
				'Thunderpike',
				'Chrome',
				'Snowfletcher',
				'Metora',
			], $weaponName);

			if ($weaponType === WeaponType::DUAL_BLADES)
				$weaponName = str_replace([
					'Dragonbone Cleaver',
				], [
					'Dragonbone Twinblades',
				], $weaponName);

			$weapon = $this->manager->getRepository('App:Weapon')->findOneBy([
				'name' => $weaponName,
				'type' => $weaponType,
			]);

			if (!$weapon) {
				$newLines = str_repeat(PHP_EOL, 4);

				echo $newLines . '>> Could not find ' . $weaponType . ' named ' . $weaponName . $newLines . PHP_EOL;

				return;
			}

			$eldersealColIndex = 6;

			if ($weaponType === WeaponType::CHARGE_BLADE)
				$eldersealColIndex = 8;

			$elderseal = str_replace([
				'-',
				'n/a',
			], '', strtolower(StringUtil::clean($cells->eq($eldersealColIndex)->text())));

			if ($weaponType === WeaponType::BOW) {
				preg_match('/Elderseal: (low|average|high)/i', $elderseal, $matches);

				if (sizeof($matches) >= 2)
					$elderseal = strtolower($matches[1]);
			}

			if (!Elderseal::isValid($elderseal))
				$weapon->removeAttribute(Attribute::ELDERSEAL);
			else
				$weapon->setAttribute(Attribute::ELDERSEAL, $elderseal);
		}
	}