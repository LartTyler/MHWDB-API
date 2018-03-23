<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreters;

	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponData;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreterInterface;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class MaterialUpgradingInterpreter implements WeaponDataInterpreterInterface {
		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return strpos(StringUtil::clean($node->text()), 'Upgrading') !== false;
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$rows = $node->filter('tr');
			$materials = [];

			for ($i = 0, $ii = $rows->count(); $i < $ii; $i++) {
				$cells = $rows->eq($i)->filter('td');

				$name = StringUtil::clean($cells->eq(0)->text());
				$amount = (int)substr(StringUtil::clean($cells->eq(1)->text()), 1);

				$materials[$name] = $amount;
			}

			$target->setUpgradeMaterials($materials);
		}
	}