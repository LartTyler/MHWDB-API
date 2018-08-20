<?php
	namespace App\Scraping\Scrapers\Helpers;

	final class FextralifeHelper {
		/**
		 * Changes correct weapon names to names that match what's on the Fextralife wiki.
		 *
		 * @param string $name
		 *
		 * @return string
		 */
		public static function toWikiWeaponName(string $name): string {
			return str_replace([
				'Sworn Rapiers+',
				'Dancing Davul',
				'Princess Panoply+',
			], [
				'Sworn Rapiers +',
				'Dancing Duval',
				'Princess Panoply +',
			], $name);
		}

		/**
		 * FextralifeHelper constructor.
		 */
		private function __construct() {
		}
	}