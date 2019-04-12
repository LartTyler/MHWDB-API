<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\Doze\Errors\ApiError;

	class MissingRequiredFieldsError extends ApiError {
		/**
		 * MissingRequiredFieldsError constructor.
		 *
		 * @param array $fields
		 */
		public function __construct(array $fields) {
			parent::__construct(
				'contrib.missing_fields',
				'The following fields must be present in your payload: ' . implode(', ', $fields)
			);
		}
	}