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
			$values = array_map(function(string $item): string {
				return (int)trim($item);
			}, explode('|', $text = StringUtil::clean($node->filter('.lead')->text())));

			if (sizeof($values) !== 2)
				throw new \RuntimeException(sprintf('Invalid attack values for %s: %s', $target->getName(), $text));

			$target->getAttack()
				->setDisplay($values[0])
				->setRaw($values[1]);

			// DEPRECATED The line below preserves BC for < 1.11.0 and will be removed in the future
			$target->setAttribute(Attribute::ATTACK, $values[0]);
		}
	}