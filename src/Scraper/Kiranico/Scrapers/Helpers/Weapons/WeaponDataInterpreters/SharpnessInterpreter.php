<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponData;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreterInterface;
	use Symfony\Component\DomCrawler\Crawler;

	class SharpnessInterpreter implements WeaponDataInterpreterInterface {
		private const SHARPNESS_NODES = [
			Attribute::SHARP_RED,
			Attribute::SHARP_ORANAGE,
			Attribute::SHARP_YELLOW,
			Attribute::SHARP_GREEN,
			Attribute::SHARP_BLUE,
			Attribute::SHARP_WHITE,
		];

		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return $node->filter('small.text-muted')->text() === 'Sharpness';
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$sharpnessNodes = $node->filter('.d-flex.flex-row')->children();

			foreach (self::SHARPNESS_NODES as $i => $sharpness) {
				$styles = $sharpnessNodes->eq($i)->attr('style');

				if (!$styles || !preg_match('/width: ?(\d+)px/', $styles, $matches))
					continue;

				$value = (int)$matches[1];

				if ($value === 0)
					break;

				$target->setAttribute($sharpness, $value);
			}
		}
	}