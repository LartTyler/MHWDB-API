<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\RestApiCommon\Error\ApiError;

	class InvalidParameterError extends ApiError {
		public function __construct(string $field, string $expected) {
			$message = sprintf('You provided an invalid value for %s; it should be %s', $field, $expected);

			parent::__construct('contrib.invalid_parameter', $message);
		}
	}