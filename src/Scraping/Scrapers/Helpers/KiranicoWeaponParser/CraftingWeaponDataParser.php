<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters\CraftingPreviousInterpreter;
	use Symfony\Component\DomCrawler\Crawler;

	class CraftingWeaponDataParser extends AbstractWeaponDataParser {
		private const DEFAULT_INTERPRETERS = [
			CraftingPreviousInterpreter::class,
		];

		/**
		 * CraftingWeaponDataParser constructor.
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
		 * {@inheritdoc}
		 */
		public function parse(Crawler $section, WeaponData $target): void {
			foreach ($this->interpreters as $interpreter)
				$interpreter->parse($section, $target);
		}
	}