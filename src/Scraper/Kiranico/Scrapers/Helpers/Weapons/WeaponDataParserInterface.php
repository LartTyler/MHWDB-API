<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons;

	use Symfony\Component\DomCrawler\Crawler;

	interface WeaponDataParserInterface {
		/**
		 * @param WeaponDataInterpreterInterface[] $interpreters
		 *
		 * @return $this
		 */
		public function setInterpreters(array $interpreters);

		/**
		 * @param WeaponDataInterpreterInterface $interpreter
		 *
		 * @return $this
		 */
		public function addInterpreter(WeaponDataInterpreterInterface $interpreter);

		/**
		 * @param Crawler    $section
		 * @param WeaponData $target
		 *
		 * @return void
		 */
		public function parse(Crawler $section, WeaponData $target): void;
	}