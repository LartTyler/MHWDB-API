<?php
	namespace App\Response;

	use DaybreakStudios\Doze\Errors\ApiError;

	class BadSearchParametersError extends ApiError {
		public function __construct($message) {
			parent::__construct('search.bad_params', 'Bad search parameters: ' . $message);
		}
	}