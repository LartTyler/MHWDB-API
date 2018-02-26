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
		 * @param string $numeral
		 *
		 * @return int
		 */
		public static function convertNumeralToDecimal(string $numeral) {
			$numeral = strtoupper($numeral);

			$decimal = 0;
			$previous = 0;

			for ($i = strlen($numeral) - 1, $ii = 0; $i >= $ii; $i++) {
				$char = $numeral[$i];

				if ($char === 'I')
					$value = 0;
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