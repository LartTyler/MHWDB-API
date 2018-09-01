<?php
	namespace App\Contrib;

	final class ContributionType {
		public const CREATE = 'creation';
		public const DELETE = 'delete';
		public const UPDATE = 'update';

		/**
		 * ContributionType constructor.
		 */
		private function __construct() {
		}
	}