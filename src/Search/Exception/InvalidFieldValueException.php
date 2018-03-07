<?php
	namespace App\Search\Exception;

	class InvalidFieldValueException extends SearchException {
		public function __construct(string $field, string $required) {
			parent::__construct(sprintf('Invalid parameter for %s; value must be a(n) %s', $field, $required));
		}
	}