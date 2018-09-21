<?php
	namespace App\Api\Exceptions;

	class SlugNotSupportedException extends \RuntimeException {
		/**
		 * SlugNotSupportedException constructor.
		 *
		 * @param string $class
		 */
		public function __construct(string $class) {
			parent::__construct(sprintf($class . ' does not support retrieval by slug'));
		}
	}