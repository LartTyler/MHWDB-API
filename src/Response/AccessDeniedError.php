<?php
	namespace App\Response;

	use DaybreakStudios\Doze\Errors\ApiError;

	class AccessDeniedError extends ApiError {
		/**
		 * AccessDeniedError constructor.
		 *
		 * @param string $reason
		 */
		public function __construct(string $reason) {
			parent::__construct('access_denied', $reason);
		}
	}