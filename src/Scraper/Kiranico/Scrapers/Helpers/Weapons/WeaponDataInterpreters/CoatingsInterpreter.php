<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponData;
	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreterInterface;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class CoatingsInterpreter implements WeaponDataInterpreterInterface {
		private const COATING_TRANSLATIONS = [
			'Cls' => 'Close Range',
			'Pow' => 'Power',
			'Par' => 'Paralysis',
			'Poi' => 'Poison',
			'Sle' => 'Sleep',
			'Bla' => 'Blast',
		];

		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return $node->filter('small.text-muted')->text() === 'Coatings';
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$coatingNodes = $node->filter('pre.mb-0 span');
			$coatings = [];

			for ($i = 0, $ii = $coatingNodes->count(); $i < $ii; $i++) {
				$coatingNode = $coatingNodes->eq($i);

				if (strpos($coatingNode->attr('class'), 'text-dark') !== false)
					continue;

				$coating = StringUtil::clean($coatingNode->text());

				if (isset(self::COATING_TRANSLATIONS[$coating]))
					$coating = self::COATING_TRANSLATIONS[$coating];

				$coatings[] = $coating;
			}

			$target->setAttribute(Attribute::COATINGS, $coatings);
		}
	}