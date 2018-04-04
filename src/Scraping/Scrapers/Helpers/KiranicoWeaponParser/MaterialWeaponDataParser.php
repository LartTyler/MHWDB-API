<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters\MaterialCraftingInterpreter;
	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters\MaterialUpgradingInterpreter;
	use Symfony\Component\DomCrawler\Crawler;

	class MaterialWeaponDataParser extends AbstractWeaponDataParser {
		private const DEFAULT_INTERPRETERS = [
			MaterialCraftingInterpreter::class,
			MaterialUpgradingInterpreter::class,
		];

		/**
		 * MaterialWeaponDataParser constructor.
		 *
		 * @param array $interpreters
		 * @param bool  $skipDefault
		 */
		public function __construct(array $interpreters = [], bool $skipDefault = false) {
			parent::__construct($interpreters);

			if (!$skipDefault)
				foreach (self::DEFAULT_INTERPRETERS as $class)
					$this->addInterpreter(new $class());
		}

		/**
		 * @param Crawler    $section
		 * @param WeaponData $target
		 */
		public function parse(Crawler $section, WeaponData $target): void {
			$headers = $section->filter('h6');
			$nodes = $section->filter('.card-body table');

			for ($i = 0, $ii = $nodes->count(); $i < $ii; $i++) {
				foreach ($this->interpreters as $interpreter) {
					if (!$interpreter->supports($headers->eq($i)))
						continue;

					$interpreter->parse($nodes->eq($i), $target);
				}
			}
		}
	}