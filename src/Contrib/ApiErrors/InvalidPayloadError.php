<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\Doze\Errors\ApiError;

	class InvalidPayloadError extends ApiError {
		/**
		 * InvalidPayloadError constructor.
		 */
		public function __construct() {
			parent::__construct('contrib.invalid_payload', 'You must provide a valid JSON payload in your request');
		}
	}