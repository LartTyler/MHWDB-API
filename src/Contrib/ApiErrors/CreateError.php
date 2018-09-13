<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\Doze\Errors\ApiError;

	class CreateError extends ApiError {
		/**
		 * UpdateError constructor.
		 */
		public function __construct() {
			parent::__construct('contrib.create_failed',
				'Could not complete your create request. Check your data and try again.');
		}
	}