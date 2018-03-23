<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreters;

	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponData;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreterInterface;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class CraftingPreviousInterpreter implements WeaponDataInterpreterInterface {
		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			$header = $node->filter('h4');

			return $header->count() && strpos($header->text(), 'upgrade path') !== false;
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$items = $node->filter('.card-body .text-center div');

			if ($items->count() < 2)
				return;
			
			$name = StringUtil::clean($items->eq($items->count() - 2)->text());

			if (strpos($name, 'creatable') !== false) {
				$target->setPreviousCraftable(true);

				$name = trim(substr($name, 0, strpos($name, '(') - 1));
			}

			$target->setCraftingPrevious(StringUtil::replaceNumeralRank($name));
		}
	}