<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponData;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreterInterface;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class BoostTypeInterpreter implements WeaponDataInterpreterInterface {
		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return $node->filter('small.text-muted')->text() === 'Kinsect Bonus';
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$boost = StringUtil::clean($node->filter('.lead')->text());

			$target->setAttribute(Attribute::IG_BOOST_TYPE, $boost);
		}
	}