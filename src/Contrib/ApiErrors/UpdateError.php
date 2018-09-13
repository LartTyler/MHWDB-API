<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\Doze\Errors\ApiError;

	class UpdateError extends ApiError {
		/**
		 * UpdateError constructor.
		 */
		public function __construct() {
			parent::__construct('contrib.update_failed',
				'Could not complete your update. Check your data and try again.');
		}
	}