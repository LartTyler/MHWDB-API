<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\MotionValue;
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

				$uri = $this->configuration->getBaseUri()->withPath('/guides/' . $weaponType);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$headers = (new Crawler($response->getBody()->getContents()))->filter('h2');

				for ($i = 0, $ii = $headers->count(); $i < $ii; $i++) {
					$header = $headers->eq($i);

					if (StringUtil::clean($header->text()) !== 'Motion Values')
						continue;

					$this->process($weaponType, $header->nextAll()->first()->filter('tr'));

					break;
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string  $weaponType
		 * @param Crawler $tableRows
		 *
		 * @return void
		 */
		protected function process(string $weaponType, Crawler $tableRows): void {
			for ($i = 0, $ii = $tableRows->count(); $i < $ii; $i++) {
				/**
				 * 0 = Name
				 * 1 = Damage type
				 * 2 = Hits
				 * 3 = Stun potency
				 * 4 = Exhaust potency
				 */
				$cells = $tableRows->eq($i)->filter('td');

				// Rows with no td elements are header rows (they only contain th elements)
				if ($cells->count() === 0)
					continue;

				$name = StringUtil::clean($cells->eq(0)->text());
				$motion = $this->manager->getRepository('App:MotionValue')->findOneBy([
					'name' => $name,
					'weaponType' => $weaponType,
				]);

				if (!$motion)
					$motion = new MotionValue($name, $weaponType);

				$motion->setDamageType(KiranicoMotionValueHelper::cleanDamageType($cells->eq(1)->text()));

				$hitString = StringUtil::clean($cells->eq(2)->text());

				if (strpos($motion->getName(), 'Spirit Helm Breaker') === 0) {
					preg_match('/^(\d+), up to (\d+) hits?$/', $hitString, $matches);

					if (sizeof($matches) !== 3) {
						throw new \RuntimeException('Could not parse hit string for ' . $weaponType . ' ' .
							$motion->getName());
					}

					$motion->setValues([
						(int)$matches[1],
						(int)$matches[2],
					]);
				}
			}
		}
	}