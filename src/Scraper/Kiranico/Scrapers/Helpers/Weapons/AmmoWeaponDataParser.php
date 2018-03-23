<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons;

	use App\Scraper\Kiranico\Scrapers\Helpers\Weapons\WeaponDataInterpreters\AmmoCapacitiesInterpreter;
	use Symfony\Component\DomCrawler\Crawler;

	class AmmoWeaponDataParser extends AbstractWeaponDataParser {
		private const DEFAULT_INTERPRETERS = [
			AmmoCapacitiesInterpreter::class,
		];

		/**
		 * AmmoWeaponDataParser constructor.
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
			foreach ($this->interpreters as $interpreter) {
				if (!$interpreter->supports($section))
					continue;

				$interpreter->parse($section, $target);
			}
		}
	}