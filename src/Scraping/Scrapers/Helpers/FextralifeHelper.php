<?php
	namespace App\Scraping\Scrapers\Helpers;

	final class FextralifeHelper {
		/**
		 * FextralifeHelper constructor.
		 */
		private function __construct() {
		}

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
		 * @param string $value
		 *
		 * @return string
		 */
		public static function toWikiSlug(string $value): string {
			return strtr($value, ' ', '+');
		}
	}