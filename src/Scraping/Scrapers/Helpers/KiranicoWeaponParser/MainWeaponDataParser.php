<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

	use App\Scraping\Scrapers\Helpers\KiranicoWeaponParser\WeaponDataInterpreters as Interpreter;
	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	class MainWeaponDataParser extends AbstractWeaponDataParser {
		const DEFAULT_INTERPRETERS = [
			// -- All Weapons --
			Interpreter\AttackInterpreter::class,
			Interpreter\RarityInterpreter::class,
			Interpreter\SlotsInterpreter::class,
			Interpreter\ElementInterpreter::class,

			// -- Melee Only --
			Interpreter\SharpnessInterpreter::class,
			Interpreter\AffinityInterpreter::class,

			// -- Switch Axe / Charge Blade --
			Interpreter\PhialTypeInterpreter::class,

			// -- Gunlance --
			Interpreter\ShellingTypeInterpreter::class,

			// -- Insect Glaive --
			Interpreter\BoostTypeInterpreter::class,

			// -- Bowguns --
			Interpreter\DeviationInterpreter::class,
			Interpreter\SpecialAmmoInterpreter::class,

			// -- Bow --
			Interpreter\CoatingsInterpreter::class,
		];

		/**
		 * MainWeaponDataParser constructor.
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
		 * @param WeaponData $data
		 *
		 * @return void
		 */
		public function parse(Crawler $section, WeaponData $data): void {
			$weaponName = StringUtil::clean($section->filter('.card-body .media-body h1 span')->text());
			$weaponName = StringUtil::replaceNumeralRank($weaponName);

			$data->setName($weaponName);

			$nodes = $section->filter('.card-footer .p-3');

			for ($i = 0, $ii = $nodes->count(); $i < $ii; $i++) {
				$node = $nodes->eq($i);

				foreach ($this->interpreters as $interpreter) {
					if (!$interpreter->supports($node))
						continue;

					$interpreter->parse($node, $data);

					break;
				}
			}
		}
	}