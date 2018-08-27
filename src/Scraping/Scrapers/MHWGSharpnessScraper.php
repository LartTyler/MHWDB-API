<?php
	namespace App\Scraping\Scrapers;

	use App\Entity\WeaponSharpness;
	use App\Game\Sharpness;
	use App\Game\WeaponType;
	use App\Scraping\Configurations\MHWGConfiguration;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ProgressAwareTrait;
	use App\Scraping\ScraperInterface;
	use App\Scraping\Scrapers\Helpers\CssHelper;
	use App\Scraping\Scrapers\Helpers\MHWGWeaponTreeHelper;
	use App\Scraping\Type;
	use App\Utility\StringUtil;
	use Doctrine\Common\Persistence\ObjectManager;
	use Psr\Http\Message\UriInterface;
	use Symfony\Component\DomCrawler\Crawler;
	use Symfony\Component\HttpFoundation\Response;

	class MHWGSharpnessScraper extends AbstractMHWGScraper implements ProgressAwareInterface {
		const SEGMENT_MAP = [
			'kr0' => Sharpness::RED,
			'kr1' => Sharpness::ORANGE,
			'kr2' => Sharpness::YELLOW,
			'kr3' => Sharpness::GREEN,
			'kr4' => Sharpness::BLUE,
			'kr5' => Sharpness::WHITE,
		];

		use ProgressAwareTrait;

		/**
		 * MHWGSharpnessScraper constructor.
		 *
		 * @param MHWGConfiguration $configuration
		 * @param ObjectManager     $manager
		 */
		public function __construct(MHWGConfiguration $configuration, ObjectManager $manager) {
			parent::__construct($configuration, Type::SHARPNESS, $manager);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function scrape(array $context = []): void {
			$subtypes = $context[ScraperInterface::CONTEXT_SUBTYPES] ?? WeaponType::all();

			$this->progressBar->append(sizeof($subtypes));

			foreach ($subtypes as $weaponType) {
				$uri = $this->getUriWithPath(MHWGWeaponTreeHelper::WEAPON_TREE_MAP[$weaponType]);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$links = (new Crawler($response->getBody()->getContents()))
					->filter('#main_1 table tr[class]:not(.th2) span > a');

				$paths = [];

				for ($i = 0, $ii = $links->count(); $i < $ii; $i++) {
					$path = $links->eq($i)->attr('href');

					// We don't store data for the Kulve Taroth weapons
					if (in_array($path, MHWGWeaponTreeHelper::KULVE_TAROTH_WEAPON_PATHS))
						continue;

					$paths[$path] = true;
				}

				$paths = array_keys($paths);

				$this->progressBar->append(sizeof($paths));

				$ordinal = 0;

				foreach ($paths as $path) {
					$ordinal = $this->process($path, $weaponType, $ordinal);

					$this->progressBar->advance();
				}

				$this->progressBar->advance();
			}

			$this->manager->flush();
		}

		/**
		 * @param string $path
		 * @param string $weaponType
		 * @param int    $ordinal
		 *
		 * @return int
		 */
		protected function process(string $path, string $weaponType, int $ordinal): int {
			$uri = $this->getConfiguration()->getBaseUri()->withPath($path);
			$response = $this->getWithRetry($uri);

			if ($response->getStatusCode() !== Response::HTTP_OK)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$boxes = (new Crawler($response->getBody()->getContents()))->filter('#main_1 .t1.th3')->first()
				->filter('tr > td:last-child .kcon');

			for ($boxIndex = 0, $boxLength = $boxes->count(); $boxIndex < $boxLength; $boxIndex++) {
				$weapon = $this->matchWeapon($weaponType, $ordinal++);

				if (!$weapon) {
					throw new \RuntimeException('Could not find weapon by type and ordinal: ' . $weaponType . ' - ' .
						$ordinal);
				}

				$bars = $boxes->eq($boxIndex)->filter('.kbox');

				$weapon->getDurability()->clear();

				for ($i = 0, $ii = $bars->count(); $i < $ii; $i++) {
					$sharpness = new WeaponSharpness();
					$segments = $bars->eq($i)->filter('span');

					for ($j = 0, $jj = $segments->count(); $j < $jj; $j++) {
						$segment = $segments->eq($j);

						$classes = CssHelper::getClasses($segment->attr('class'));
						$color = null;

						foreach ($classes as $class) {
							if (isset(self::SEGMENT_MAP[$class])) {
								$color = self::SEGMENT_MAP[$class];

								break;
							}
						}

						if ($color === null)
							break;

						$styles = CssHelper::toStyleMap($segment->attr('style'));

						if (!isset($styles['width'])) {
							throw new \RuntimeException('Something is wrong with the sharpness bars on ' . $uri .
								' at box #' . $i);
						}

						$hits = StringUtil::toNumber($styles['width']) / 0.4;

						call_user_func([$sharpness, 'set' . ucfirst($color)], $hits);
					}

					$weapon->getDurability()->add($sharpness);
				}
			}

			return $ordinal;
		}

		/**
		 * @param string $path
		 *
		 * @return UriInterface
		 */
		public function getUriWithPath(string $path): UriInterface {
			return $this->getConfiguration()->getBaseUri()->withPath('/data/' . ltrim($path, '/'));
		}
	}