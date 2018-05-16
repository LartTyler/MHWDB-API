<?php
	namespace App\Response;

	use DaybreakStudios\Doze\Errors\ApiError;

	class BadProjectionObjectError extends ApiError {
		/**
		 * BadProjectionObjectError constructor.
		 */
		public function __construct() {
			parent::__construct('search.malformed_projection',
				'Your projection object was invalid; check your syntax and try again');
		}
	}