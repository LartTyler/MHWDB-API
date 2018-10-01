<?php
	namespace App\Contrib\Exceptions;

	class IntegrityException extends ContribException {
		/**
		 * @param string $field
		 * @param string $referenceName
		 *
		 * @return static
		 */
		public static function missingReference(string $field, string $referenceName) {
			return new static(sprintf('The value of %s should be the ID of an existing %s', $field, $referenceName));
		}

		/**
		 * @param string $field
		 * @param int    $collisionId
		 *
		 * @return IntegrityException
		 */
		public static function duplicateUniqueValue(string $field, int $collisionId) {
			return new static(
				sprintf('The value of %s must be a unique value, but collides with #%d', $field, $collisionId)
			);
		}
	}