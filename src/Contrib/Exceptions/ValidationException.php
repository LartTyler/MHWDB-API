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
			return static::invalidFieldValue(
				$field,
				'It should be ' . StringUtil::getIndefinateArticle($expected) . ' ' . $expected
			);
		}

		/**
		 * @param string $field
		 * @param string $explanation
		 *
		 * @return static
		 */
		public static function invalidFieldValue(string $field, string $explanation) {
			return new static(
				sprintf(
					'You provided an invalid value for %s; %s',
					$field,
					$explanation
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

		/**
		 * @param string $field
		 *
		 * @return static
		 */
		public static function fieldNotSupported(string $field) {
			return new static('The API does not support updating ' . $field);
		}
	}