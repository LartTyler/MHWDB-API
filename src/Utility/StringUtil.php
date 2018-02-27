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
			return strtolower(str_replace([
				' ',
				'\'',
			], [
				'-',
				'',
			], $string));
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

			return $title . ' ' . StringUtil::convertNumeralToDecimal($rank);
		}

		/**
		 * @param string $numeral
		 *
		 * @return int
		 */
		public static function convertNumeralToDecimal(string $numeral) {
			$numeral = strtoupper($numeral);

			$decimal = 0;
			$previous = 0;

			for ($i = strlen($numeral) - 1; $i >= 0; $i--) {
				$char = $numeral[$i];

				if ($char === 'I')
					$value = 1;
				else if ($char === 'V')
					$value = 5;
				else if ($char === 'X')
					$value = 10;
				else if ($char === 'L')
					$value = 50;
				else
					throw new \InvalidArgumentException($char . ' is not a supported numeral');

				if ($value < $previous)
					$decimal -= $value;
				else
					$decimal += $value;
			}

			return $decimal;
		}
	}