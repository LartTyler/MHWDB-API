<?php
	namespace App\Contrib\Exceptions;

	use App\Contrib\Delete\DeleteFailureReasonInterface;

	class DeleteFailedException extends \RuntimeException {
		/**
		 * @var DeleteFailureReasonInterface
		 */
		protected $failureReason;

		/**
		 * DeleteFailedException constructor.
		 *
		 * @param DeleteFailureReasonInterface $failureReason
		 */
		public function __construct(DeleteFailureReasonInterface $failureReason) {
			parent::__construct('Could not delete object: ' . $failureReason->getMessage());

			$this->failureReason = $failureReason;
		}

		/**
		 * @return DeleteFailureReasonInterface
		 */
		public function getFailureReason(): DeleteFailureReasonInterface {
			return $this->failureReason;
		}
	}