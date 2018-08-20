<?php
	namespace App\Scraping\Scrapers\Helpers;

	final class CssHelper {
		/**
		 * CssHelper constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $styles
		 *
		 * @return string[]
		 */
		public static function toStyleMap(string $styles): array {
			$map = [];
			$split = array_map(function(string $value): string {
				return trim($value);
			}, explode(';', $styles));

			foreach ($split as $item) {
				$key = substr($item, 0, strpos($item, ':'));
				$value = trim(substr($item, strpos($item, ':') + 1));

				$map[$key] = $value;
			}

			return $map;
		}

		/**
		 * @param string $classList
		 *
		 * @return string[]
		 */
		public static function getClasses(string $classList): array {
			return explode(' ', preg_replace('/\\s+/', ' ', $classList));
		}
	}