<?php
	namespace App\Utility;

	final class RomanNumeral {
		private const NUMERALS = [
			'I' => 1,
			'V' => 5,
			'X' => 10,
			'L' => 50,
		];

		/**
		 * @param string $numeral
		 *
		 * @return int
		 */
		public static function toDecimal(string $numeral): int {
			$numeral = strtoupper($numeral);

			$decimal = 0;
			$previous = 0;

			for ($i = strlen($numeral) - 1; $i >= 0; $i--) {
				$char = $numeral[$i];

				if (isset(self::NUMERALS[$char]))
					$value = self::NUMERALS[$char];
				else
					throw new \InvalidArgumentException($char . ' is not a supported numeral');

				if ($value < $previous)
					$decimal -= $value;
				else
					$decimal += $value;
			}

			return $decimal;
		}

		/**
		 * @param int $decimal
		 *
		 * @return string
		 */
		public static function toNumeral(int $decimal): string {
			$numeral = '';

			foreach (array_reverse(self::NUMERALS, true) as $char => $value) {
				$numeral .= str_repeat($char, floor($decimal / $value));

				$decimal %= $value;
			}

			return $numeral;
		}

		/**
		 * RomanNumeral constructor.
		 */
		private function __construct() {
		}
	}