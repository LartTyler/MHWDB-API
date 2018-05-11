<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters;

	use App\Game\Attribute;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\Element;
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

			// Handles some weapons (such as Fire and Ice) having more than one element
			if (strpos($raw, '/') !== false)
				$rawElements = array_map(function(string $part): string {
					return trim($part);
				}, explode('/', $raw));
			else
				$rawElements = [$raw];

			foreach ($rawElements as $i => $rawElement) {
				$element = new Element();

				if (strpos($rawElement, '(') === 0) {
					$element->setHidden(true);

					$rawElement = substr($rawElement, 1, -1);
				}

				$element
					->setDamage((int)strtok($rawElement, ' '))
					->setType(strtok(''));

				$target->setElement($element);
			}
		}
	}