<?php
	namespace App\Scraping\Scrapers\Helpers;

	final class FextralifeHelper {
		/**
		 * @param string $name
		 *
		 * @return string
		 */
		public static function fixSkillName(string $name): string {
			return str_replace([
				' / ',
				'Great Luck',
				'Carving Master',
			], [
				'/',
				'Good Luck',
				'Carving Pro',
			], $name);
		}

		/**
		 * FextralifeHelper constructor.
		 */
		private function __construct() {
		}
	}