<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\Doze\Errors\ApiError;

	class DeleteError extends ApiError {
		/**
		 * DeleteError constructor.
		 *
		 * @param string $message
		 */
		public function __construct(string $message) {
			parent::__construct('contrib.delete_failed', $message);
		}
	}