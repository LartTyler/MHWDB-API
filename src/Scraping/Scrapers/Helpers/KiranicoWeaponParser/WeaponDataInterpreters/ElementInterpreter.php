<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponData;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreterInterface;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class ElementInterpreter implements WeaponDataInterpreterInterface {
		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return $node->filter('small.text-muted')->text() === 'Element';
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$raw = StringUtil::clean($node->filter('.lead')->text());

			if (strpos($raw, '(') === 0) {
				$target->setAttribute(Attribute::ELEM_HIDDEN, true);

				$raw = substr($raw, 1, -1);
			}

			$target
				->setAttribute(Attribute::ELEM_DAMAGE, (int)strtok($raw, ' '))
				->setAttribute(Attribute::ELEM_TYPE, strtok(''));
		}
	}