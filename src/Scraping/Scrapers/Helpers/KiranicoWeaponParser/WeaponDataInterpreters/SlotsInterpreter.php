<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters;

	use App\Scraping\Scrapers\Helpers\KiranicoHelper;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponData;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreterInterface;
	use Symfony\Component\DomCrawler\Crawler;

	class SlotsInterpreter implements WeaponDataInterpreterInterface {
		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return $node->filter('small.text-muted')->text() === 'Slots';
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$target->setSlots(KiranicoHelper::getSlots($node->filter('.lead .zmdi')));

			// DEPRECATED This preserves BC for < 1.8.0, and will be removed in the future
			foreach ($target->getSlots() as $rank) {
				$key = 'slotsRank' . $rank;

				if ($count = $target->getAttribute($key))
					$target->setAttribute($key, $count + 1);
				else
					$target->setAttribute($key, 1);
			}
		}
	}