<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\MotionValue;
	use App\Game\DamageType;
	use App\Game\WeaponType;
	use App\Scraping\AbstractScraper;
	use App\Scraping\Configurations\KiranicoConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\ScraperInterface;
	use App\Scraping\Scrapers\Helpers\KiranicoMotionValueHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class KiranicoMotionValuesScraper extends AbstractScraper implements ProgressAwareInterface {
		use ProgressAwareTrait;

		/**
		 * @var KiranicoConfiguration
		 */
		protected $configuration;

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * KiranicoMotionValuesScraper constructor.
		 *
		 * @param KiranicoConfiguration $configuration
		 * @param ObjectManager         $manager
		 */
		public function __construct(KiranicoConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::MOTION_VALUES);

			$this->manager = $manager;
		}

		/**
		 * {@inheritdoc}
		 */
		public function scrape(array $context = []): void {
			$subtypes = $context[ScraperInterface::CONTEXT_SUBTYPES] ?? WeaponType::all();

			$this->progressBar->append(sizeof($subtypes));

			foreach ($subtypes as $weaponType) {
				if (!in_array($weaponType, $subtypes))
					continue;

				$uri = $this->configuration->getBaseUri()->withPath('/guide/' . $weaponType);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$headers = (new Crawler($response->getBody()->getContents()))->filter('.guide > h2');

				for ($i = 0, $ii = $headers->count(); $i < $ii; $i++) {
					$header = $headers->eq($i);
					$headerText = StringUtil::clean($header->text());

					if (!in_array($headerText, ['Motion Values', 'Ammo Motion Values']))
						continue;

					$tableType = $headerText === 'Ammo Motion Values' ? 'ammo' : 'normal';

					/** @var Crawler[] $tables */
					$tables = [
						$tableType => $header->nextAll()->filter('table')->first()
					];

					if ($weaponType === WeaponType::HEAVY_BOWGUN)
						$tables['normal'] = $header->previousAll()->filter('table')->first();

					foreach ($tables as $tableType => $table)
						$this->process($weaponType, $table->filter('tr'), $tableType === 'ammo');
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string  $weaponType
		 * @param Crawler $tableRows
		 *
		 * @param bool    $isAmmo
		 *
		 * @return void
		 */
		protected function process(string $weaponType, Crawler $tableRows, bool $isAmmo): void {
			for ($i = 0, $ii = $tableRows->count(); $i < $ii; $i++) {
				/**
				 * 0 = Name
				 * 1 = Damage type
				 * 2 = Hits
				 * 3 = Stun potency
				 * 4 = Exhaust potency
				 *
				 * If $isAmmo = true, the "Damage Type" column doesn't exist, and hits, stun, and exhaust should have
				 * their indexes decreased by 1 (to 1, 2, and 3 respectively).
				 */
				$cells = $tableRows->eq($i)->filter('td');

				// Rows with no td elements are header rows (they only contain th elements)
				if ($cells->count() === 0)
					continue;

				$name = StringUtil::clean($cells->eq(0)->text());
				$damageType = KiranicoMotionValueHelper::cleanDamageType($cells->eq(1)->text());

				if (in_array($weaponType, [WeaponType::LIGHT_BOWGUN, WeaponType::HEAVY_BOWGUN])) {
					if (!$damageType || preg_match('/\d/', $damageType) !== 0)
						$damageType = DamageType::PROJECTILE;
				}

				$hitNode = $cells->eq($isAmmo ? 1 : 2);

				$hitNode->filter('del')->each(function(Crawler $crawler): void {
					foreach ($crawler as $node)
						$node->parentNode->removeChild($node);
				});

				$hitString = StringUtil::clean($hitNode->text());

				$hitString = preg_replace_callback('/(\d+) *x *(\d+)/', function(array $matches) {
					return str_repeat($matches[1] . '+', (int)$matches[2]);
				}, $hitString);

				$hitString = preg_replace_callback('/(\d+), up to (\d+) hits/', function(array $matches) {
					return str_repeat($matches[1] . '+', (int)$matches[2]);
				}, $hitString);

				$hitString = trim(preg_replace('/[^\d+]+/', '', $hitString), '+');
				$hitValues = array_map(function(string $value): int {
					return (int)trim($value);
				}, explode('+', $hitString));

				$stun = StringUtil::clean($cells->eq($isAmmo ? 2 : 3)->text());

				if (is_numeric($stun))
					$stun = (int)$stun;
				else
					$stun = null;

				$exhaust = StringUtil::clean($cells->eq($isAmmo ? 3 : 4)->text());

				if (is_numeric($exhaust))
					$exhaust = (int)$exhaust;
				else
					$exhaust = null;

				if (strpos($name, 'Spirit Helm Breaker (White/Yellow)') === 0) {
					foreach (['White', 'Yellow'] as $gauge) {
						$name = 'Spirit Helm Breaker (' . $gauge . ')';

						$this->addMotionValue($name, $weaponType, $damageType, $hitValues, $stun, $exhaust);
					}
				} else
					$this->addMotionValue($name, $weaponType, $damageType, $hitValues, $stun, $exhaust);
			}
		}

		/**
		 * @param string      $name
		 * @param string      $weaponType
		 * @param null|string $damageType
		 * @param int[]       $hits
		 * @param int|null    $stun
		 * @param int|null    $exhaust
		 */
		protected function addMotionValue(
			string $name,
			string $weaponType,
			?string $damageType,
			array $hits,
			?int $stun,
			?int $exhaust
		): void {
			$motion = $this->manager->getRepository('App:MotionValue')->findOneBy([
				'name' => $name,
				'weaponType' => $weaponType,
			]);

			if (!$motion)
				$this->manager->persist($motion = new MotionValue($name, $weaponType));

			$motion
				->setDamageType($damageType)
				->setHits($hits)
				->setStun($stun)
				->setExhaust($exhaust);
		}
	}