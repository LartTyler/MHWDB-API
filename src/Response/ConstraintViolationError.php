<?php
	namespace App\Response;

	use DaybreakStudios\Doze\Errors\ApiError;
	use Symfony\Component\Validator\ConstraintViolationInterface;
	use Symfony\Component\Validator\ConstraintViolationListInterface;

	class ConstraintViolationError extends ApiError {
		/**
		 * ValidationError constructor.
		 *
		 * @param ConstraintViolationListInterface $errors
		 */
		public function __construct(ConstraintViolationListInterface $errors) {
			$violations = [];

			/** @var ConstraintViolationInterface $error */
			foreach ($errors as $error) {
				$violations[$error->getPropertyPath()] = [
					'code' => $error->getCode(),
					'path' => $error->getPropertyPath(),
					'message' => $error->getMessage(),
				];
			}

			parent::__construct(
				'constraint_violation',
				'One or more fields did not pass validation',
				null,
				[
					'violations' => $violations,
				]
			);
		}
	}