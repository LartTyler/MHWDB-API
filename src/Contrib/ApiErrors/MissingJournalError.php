<?php
	namespace App\Contrib\ApiErrors;

	use DaybreakStudios\Doze\Errors\ApiError;

	class MissingJournalError extends ApiError {
		public function __construct(string $type) {
			parent::__construct('contrib.journal_missing', 'Could not find a valid .journal.json for ' . $type);
		}
	}