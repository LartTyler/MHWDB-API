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
				'Kadachi Striker+',
				'Tyrannis Glaive 1'
			], [
				'Sworn Rapiers +',
				'Dancing Duval',
				'Princess Panoply +',
				'Kadachi Striker +',
				'Tyrannis Glaive'
			], $name);
		}

		/**
		 * @param string $value
		 *
		 * @return string
		 */
		public static function toWikiSlug(string $value): string {
			$value = str_replace([
				'(',
				')',
				'"'
			], '', $value);

			if ($value === 'King Beetle Thorax')
				$value = 'King Beetle Thorax ';
			else if ($value === 'Rath Heart Braces Alpha')
				$value = 'Rath Heart Vambraces Alpha';
			else if ($value === 'Rath Heart Braces Beta')
				$value = 'Rath Heart Vambraces Beta';
			else if ($value === 'Faux Felyne Alpha')
				$value = 'Faux Felyne Alpha Helm';
			else if ($value === 'Diablos Nero Braces Alpha')
				$value = 'Diablos Nero Vambraces Alpha';
			else if ($value === 'Diablos Nero Braces Beta')
				$value = 'Diablos Nero Vambraces Beta';
			else if ($value === 'Kulu-Yaku Head Alpha')
				$value = 'Kulu-Ya-Ku Head Alpha';

			return strtr($value, ' ', '+');
		}

		/**
		 * @param string $path
		 *
		 * @return string
		 */
		public static function fixWikiImageLink(string $path): string {
			return str_replace([
				'horntaur',
				'high_metal_vambraces_alpha_female'
			], [
				'hornetaur',
				'high_metal_braces_alpha_female',
			], $path);
		}
	}