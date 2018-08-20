<?php
	namespace App\Scraping\Scrapers\Helpers;

	final class KiranicoHelper {
		/**
		 * @param string $fullName
		 *
		 * @return array
		 */
		public static function splitNameAndLevel(string $fullName): array {
			preg_match('/^([^\d]+)(?: (\d+))?$/', $fullName, $matches);

			if (sizeof($matches) < 2)
				throw new \RuntimeException($fullName . ' is not a parseable name');

			return [
				$matches[1],
				(int)($matches[2] ?? 1),
			];
		}

		/**
		 * KiranicoHelper constructor.
		 */
		private function __construct() {
		}
	}