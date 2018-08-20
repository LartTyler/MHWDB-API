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
	use App\Scraping\Scrapers\Helpers\MHWGHelper;
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
				$uri = $this->getUriWithPath(MHWGHelper::WEAPON_TREE_MAP[$weaponType]);
				$response = $this->getWithRetry($uri);

				if ($response->getStatusCode() !== Response::HTTP_OK)
					throw new \RuntimeException('Could not retrieve ' . $uri);

				$links = (new Crawler($response->getBody()->getContents()))
					->filter('#main_1 table tr[class]:not(.th2) span > a');

				$this->progressBar->append($links->count());

				$ordinal = 0;

				for ($i = 0, $ii = $links->count(); $i < $ii; $i++) {
					$path = $links->eq($i)->attr('href');

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

			for ($i = 0, $ii = $boxes->count(); $i < $ii; $i++) {
				$weapon = $this->matchWeapon($weaponType, $ordinal++);

				if (!$weapon) {
					throw new \RuntimeException('Could not find weapon by type and ordinal: ' . $weaponType . ' - ' .
						$ordinal);
				}

				$bars = $boxes->eq($i)->filter('.kbox');

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

				// Preserves BC for < 1.13.0
				// TODO Deprecated: remove this on 2018-08-25
				/** @var WeaponSharpness $baseSharpness */
				$baseSharpness = $weapon->getDurability()->first();

				$weapon->getSharpness()
					->setRed(MHWGHelper::toOldSharpnessValue($baseSharpness->getRed()))
					->setOrange(MHWGHelper::toOldSharpnessValue($baseSharpness->getOrange()))
					->setYellow(MHWGHelper::toOldSharpnessValue($baseSharpness->getYellow()))
					->setGreen(MHWGHelper::toOldSharpnessValue($baseSharpness->getGreen()))
					->setBlue(MHWGHelper::toOldSharpnessValue($baseSharpness->getBlue()))
					->setWhite(MHWGHelper::toOldSharpnessValue($baseSharpness->getWhite()));
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