<?php
	namespace App\Api\Exceptions;

	class DeleteFailedException extends \RuntimeException {
		/**
		 * DeleteFailedException constructor.
		 *
		 * @param string $message
		 */
		public function __construct(string $message) {
			parent::__construct('Cannot delete object: ' . $message);
		}

		/**
		 * @param string $what
		 * @param int[]  $ids
		 *
		 * @return DeleteFailedException
		 */
		public static function dependentObjects(string $what, array $ids) {
			return new static('There are ' . $what . ' that depend on this object: [' . implode(', ', $ids) . ']');
		}
	}