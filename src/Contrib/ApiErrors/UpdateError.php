<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\Doze\Errors\ApiError;

	class UpdateError extends ApiError {
		/**
		 * UpdateError constructor.
		 *
		 * @param string $message
		 */
		public function __construct(string $message) {
			parent::__construct('contrib.update_failed', 'Could not update: ' . $message);
		}
	}