<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons;

	final class ParserType {
		const MAIN = 'main';
		const AMMO = 'ammo';
		const UPGRADE = 'upgrade';
		const MATERIALS = 'materials';

		/**
		 * ParserType constructor.
		 */
		private function __construct() {
		}
	}