<?php
	namespace App\Contrib\Exceptions;

	use App\Utility\StringUtil;

	class ValidationException extends ContribException {
		/**
		 * @param string $field
		 * @param string $expected
		 *
		 * @return static
		 */
		public static function invalidFieldType(string $field, string $expected) {
			return new static(
				sprintf(
					'You provided an invalid value for %s; it should be %s %s',
					$field,
					StringUtil::getIndefinateArticle($expected),
					$expected
				)
			);
		}

		/**
		 * @param string[] $fields
		 *
		 * @return static
		 */
		public static function missingFields(array $fields) {
			return new static(
				sprintf('You must provide a value for the following field(s): [%s]', implode(', ', $fields))
			);
		}
	}