<?php
	namespace App\Security;

	final class Role {
		public const ADMIN = 'ROLE_ADMIN';
		public const EDITOR = 'ROLE_EDITOR';
		public const USER = 'ROLE_USER';

		/**
		 * Role constructor.
		 */
		private function __construct() {
		}
	}