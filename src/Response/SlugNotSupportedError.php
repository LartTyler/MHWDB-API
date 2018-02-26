<?php
	namespace App\Response;

	use DaybreakStudios\Doze\Errors\ApiError;

	class SlugNotSupportedError extends ApiError {
		/**
		 * SlugNotSupportedError constructor.
		 *
		 * @param int|null $httpStatus
		 */
		public function __construct(?int $httpStatus = null) {
			parent::__construct('slug_not_supported', 'This resource cannot be retrieved using a slug', $httpStatus);
		}
	}