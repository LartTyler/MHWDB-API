<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreterInterface;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponData;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class AttackInterpreter implements WeaponDataInterpreterInterface {
		/**
		 * {@inheritdoc}
		 */
		public function supports(Crawler $node): bool {
			return strpos($node->filter('small.text-muted')->text(), 'Attack') !== false;
		}

		/**
		 * {@inheritdoc}
		 */
		public function parse(Crawler $node, WeaponData $target): void {
			$value = explode(' ', StringUtil::clean($node->filter('.lead')->text()))[0];

			$target->setAttribute(Attribute::ATTACK, (int)$value);
		}
	}