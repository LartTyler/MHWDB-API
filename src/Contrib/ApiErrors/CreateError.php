<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\Doze\Errors\ApiError;

	class CreateError extends ApiError {
		/**
		 * CreateError constructor.
		 *
		 * @param string $message
		 */
		public function __construct(string $message) {
			parent::__construct('contrib.create_failed', 'Could not create: ' . $message);
		}
	}