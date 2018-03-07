<?php
	namespace App\Search\Exception;

	class UnknownOperatorException extends SearchException {
		/**
		 * UnknownOperatorException constructor.
		 *
		 * @param string $operator
		 */
		public function __construct(string $operator) {
			parent::__construct('Unrecognized operator: ' . $operator);
		}
	}