<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponData;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreterInterface;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class AmmoCapacitiesInterpreter implements WeaponDataInterpreterInterface {
		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return strpos(StringUtil::clean($node->filter('.card-header')->text()), 'ammo table') !== false;
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$capacityNodes = $node->filter('.card-body .row div table tr');
			$capacities = [];

			for ($i = 0, $ii = $capacityNodes->count(); $i < $ii; $i++) {
				$cells = $capacityNodes->eq($i)->filter('td');

				$name = strtolower(StringUtil::clean($cells->eq(0)->text()));
				$capacities[$name] = [];

				for ($j = 1, $jj = $cells->count(); $j < $jj; $j++)
					$capacities[$name][] = (int)StringUtil::clean($cells->eq($j)->text());
			}

			$target->setAttribute(Attribute::AMMO_CAPACITIES, $capacities);
		}
	}