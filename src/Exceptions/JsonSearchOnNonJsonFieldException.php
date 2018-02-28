<?php
	namespace App\Exceptions;

	class JsonSearchOnNonJsonFieldException extends \RuntimeException {
		/**
		 * JsonSearchOnNonJsonFieldException constructor.
		 *
		 * @param string $field
		 */
		public function __construct(string $field) {
			parent::__construct(sprintf('Cannot search by child attributes on ' . $field));
		}
	}