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

				// DEPRECATED The region below preserves BC for < 1.9.0 and will be removed in the future
				// region Old Element Storage
				if ($element->isHidden()) {
					if ($i === 0)
						$hiddenKey = Attribute::ELEM_HIDDEN;
					else if ($i === 1)
						$hiddenKey = Attribute::ELEM_HIDDEN_2;
					else
						throw new \RuntimeException($target->getName() . ' has more than two elements!');

					$target->setAttribute($hiddenKey, true);
				}

				if ($i === 0) {
					$typeKey = Attribute::ELEM_TYPE;
					$damageKey = Attribute::ELEM_DAMAGE;
				} else if ($i === 1) {
					$typeKey = Attribute::ELEM_TYPE_2;
					$damageKey = Attribute::ELEM_DAMAGE_2;
				} else
					throw new \RuntimeException($target->getName() . ' has more than two elements!');

				$target
					->setAttribute($damageKey, $element->getDamage())
					->setAttribute($typeKey, $element->getType());
				// endregion
			}
		}
	}