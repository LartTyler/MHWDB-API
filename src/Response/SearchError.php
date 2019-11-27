<?php
	namespace App\Response;

	use DaybreakStudios\RestApiCommon\Error\ApiError;

	class SearchError extends ApiError {
		/**
		 * SearchError constructor.
		 *
		 * @param string $message
		 */
		public function __construct(string $message) {
			parent::__construct('search.error', 'An error occurred while running your search: ' . $message);
		}
	}