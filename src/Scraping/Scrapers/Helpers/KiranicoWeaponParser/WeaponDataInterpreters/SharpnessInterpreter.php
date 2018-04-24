<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Game\Sharpness;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponData;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreterInterface;
	use Symfony\Component\DomCrawler\Crawler;

	class SharpnessInterpreter implements WeaponDataInterpreterInterface {
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
			$sharpnessNodes = $node->filter('.d-flex.flex-row');

			// Fixes Kiranico displaying the Sharpness field on weapons it shouldn't (probably a temp. bug)
			if ($sharpnessNodes->count() === 0)
				return;

			$sharpObject = $target->getSharpness();
			$sharpnessNodes = $sharpnessNodes->children();

			foreach (Sharpness::ALL as $i => $sharpness) {
				$styles = $sharpnessNodes->eq($i)->attr('style');

				if (!$styles || !preg_match('/width: ?(\d+)px/', $styles, $matches))
					continue;

				$value = (int)$matches[1];

				if ($value === 0)
					break;

				$method = 'set' . ucfirst($sharpness);

				if (!method_exists($sharpObject, $method))
					throw new \RuntimeException('Could not find method named ' . $method);

				call_user_func([$sharpObject, $method], $value);

				// DEPRECATED The code below preserves BC for < 1.9.0 and will be removed in the future
				$target->setAttribute('sharpness' . ucfirst($sharpness), $value);
			}
		}
	}