<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponData;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreterInterface;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class AffinityInterpreter implements WeaponDataInterpreterInterface {
		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return strpos($node->filter('small.text-muted')->text(), 'Affinity') !== false;
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$value = str_replace(['+', '%'], '', StringUtil::clean($node->filter('.lead')->text()));

			$target->setAttribute(Attribute::AFFINITY, (int)$value);
		}
	}