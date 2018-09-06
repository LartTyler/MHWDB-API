<?php
	namespace App\Security;

	final class Role {
		public const ADMIN = 'ROLE_ADMIN';
		public const EDITOR = 'ROLE_EDITOR';
		public const USER = 'ROLE_USER';

		/**
		 * @var string[]|null
		 */
		private static $roles = null;

		/**
		 * Role constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$roles === null)
				self::$roles = array_values((new \ReflectionClass(self::class))->getConstants());

			return self::$roles;
		}
	}