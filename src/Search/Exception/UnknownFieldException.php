<?php
	namespace App\Search\Exception;

	class UnknownFieldException extends \Exception {
		/**
		 * UnknownFieldException constructor.
		 *
		 * @param string $field
		 */
		public function __construct(string $field) {
			parent::__construct('Unknown field: ' . $field);
		}
	}