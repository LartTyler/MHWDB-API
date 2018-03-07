<?php
	namespace App\Response;

	use DaybreakStudios\Doze\Errors\ApiError;

	class BadQueryObjectError extends ApiError {
		/**
		 * BadQueryObjectError constructor.
		 */
		public function __construct() {
			parent::__construct('search.malformed_query', 'Your query object was invalid; check your syntax and try again');
		}
	}