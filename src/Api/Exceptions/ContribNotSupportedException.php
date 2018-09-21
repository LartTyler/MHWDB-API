<?php
	namespace App\Api\Exceptions;

	class ContribNotSupportedException extends \Exception {
		/**
		 * ContribNotSupportedException constructor.
		 *
		 * @param string $class
		 */
		public function __construct(string $class) {
			parent::__construct($class . ' does not support updates through the contribution system; provide a ' .
				'contribType in the constructor to enable support');
		}
	}