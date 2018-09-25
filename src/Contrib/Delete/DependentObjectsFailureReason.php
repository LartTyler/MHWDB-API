<?php
	namespace App\Contrib\Delete;

	class DependentObjectsFailureReason implements DeleteFailureReasonInterface {
		/**
		 * @var string
		 */
		protected $message;

		/**
		 * DependentObjectsFailureReason constructor.
		 *
		 * @param string $objectName
		 * @param array  $ids
		 * @param int    $totalObjects
		 */
		public function __construct(string $objectName, array $ids, int $totalObjects) {
			$idList = implode(', ', $ids);
			$diff = $totalObjects - sizeof($ids);

			if ($diff > 0)
				$idList .= sprintf(', ...and %d other%s', $diff, $diff !== 1 ? 's' : '');

			$this->message = sprintf(
				'The API has other %s objects that depend on this object: [%s]',
				$objectName,
				$idList
			);
		}

		/**
		 * @return string
		 */
		public function getMessage(): string {
			return $this->message;
		}
	}