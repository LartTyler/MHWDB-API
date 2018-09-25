<?php
	namespace App\Contrib\Delete;

	interface DeleteFailureReasonInterface {
		/**
		 * @return string
		 */
		public function getMessage(): string;
	}