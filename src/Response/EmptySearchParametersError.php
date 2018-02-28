<?php
	namespace App\Response;

	use DaybreakStudios\Doze\Errors\ApiError;

	class EmptySearchParametersError extends ApiError {
		/**
		 * EmptySearchParametersError constructor.
		 */
		public function __construct() {
			parent::__construct('search.empty_params', 'Your search query must include at least one field to search by');
		}
	}