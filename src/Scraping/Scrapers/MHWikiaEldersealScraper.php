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
		 * MHWikiaEldersealScraper constructor.
		 *
		 * @param MHWikiaConfiguration $configuration
		 * @param ObjectManager        $manager
		 */
		public function __construct(MHWikiaConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::ELDERSEAL);

			$this->manager = $manager;
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

				$uri = $this->configuration->getBaseUri()->withPath($path);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$crawler = (new Crawler($response->getBody()->getContents()))
					->filter('#mw-content-text .wikitable.hover tr');

				$count = $crawler->count();

				$this->progressBar->append($count);

				for ($i = 0; $i < $count; $i++) {
					$row = $crawler->eq($i);

					if ($row->children()->first()->nodeName() === 'th') {
						$this->progressBar->advance();

						continue;
					}

					$this->process($row, $weaponType);

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

			$nameAnchors = $cells->eq(0)->filter('a');
			$weaponName = StringUtil::clean($nameAnchors->last()->text());

			if ($weaponName === '→')
				$weaponName = StringUtil::clean($nameAnchors->eq($nameAnchors->count() - 2)->text());

			$weaponName = StringUtil::replaceNumeralRank($weaponName);

			// General typo correction
			$weaponName = str_replace([
				'’',
				'Exterminator\'s',
				'Eraadication',
			], [
				'\'',
				'Extermination\'s',
				'Eradication',
			], $weaponName);

			// Targeted type correction
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

			$elderseal = str_replace([
				'-',
				'n/a',
			], '', strtolower(StringUtil::clean($cells->eq(6)->text())));

			if (!Elderseal::isValid($elderseal))
				$weapon->removeAttribute(Attribute::ELDERSEAL);
			else
				$weapon->setAttribute(Attribute::ELDERSEAL, $elderseal);
		}
	}