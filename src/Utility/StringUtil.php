<?php
	namespace App\Utility;

	final class StringUtil {
		/**
		 * StringUtil constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 */
		public static function toSlug(string $string): string {
			return strtolower(
				str_replace(
					[
						' ',
						'\'',
						'"',
					],
					[
						'-',
						'',
						'',
					],
					$string
				)
			);
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 */
		public static function replaceNumeralRank(string $string): string {
			$lastChar = $string[strlen($string) - 1];

			// A valid numeral ranked item name will always end in one of the following (case-sensitive)
			if (!in_array($lastChar, ['I', 'V', 'X', 'L']))
				return $string;

			$pos = strrpos($string, ' ');

			$rank = trim(substr($string, $pos + 1));
			$title = trim(substr($string, 0, $pos));

			return $title . ' ' . RomanNumeral::toDecimal($rank);
		}

		/**
		 * Cleans a string of leading and trailing whitespace, and also consolidates consecutive whitespace into a
		 * single space.
		 *
		 * Note: This method WILL replace newline characters with a single space, so if you need the preserve
		 * newlines, consider an alternative method.
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		public static function clean(string $string): string {
			return trim(preg_replace('/\s+/', ' ', $string));
		}

		/**
		 * Extracts a number from a given string. Numbers are extracted from the start of the string, and extraction
		 * ends when a character is encountered that isn't a number or a period.
		 *
		 * @param string $string
		 *
		 * @return float|int
		 */
		public static function toNumber(string $string) {
			$numeric = '';

			for ($i = 0, $ii = strlen($string); $i < $ii; $i++) {
				$char = $string[$i];

				if ($char === '.' || is_numeric($char))
					$numeric .= $char;
				else
					break;
			}

			if (strpos($numeric, '.'))
				return (float)$numeric;

			return (int)$numeric;
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 */
		public static function camelize(string $string): string {
			return lcfirst(self::classify($string));
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 */
		public static function classify(string $string): string {
			return str_replace(' ', '', ucwords(strtr($string, '_-', '  ')));
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 */
		public static function getIndefinateArticle(string $string): string {
			$char = trim($string)[0];

			if (in_array($char, ['a', 'e', 'i', 'o', 'u']))
				return 'an';

			return 'a';
		}

		/**
		 * @param string $string
		 * @param array  $values
		 *
		 * @return string
		 */
		public static function interpolate(string $string, array $values): string {
			foreach ($values as $key => $value)
				$string = str_replace(':' . $key, $value, $string);

			return $string;
		}
	}