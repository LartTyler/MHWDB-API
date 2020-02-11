<?php
	namespace App\Security;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	final class Role {
		use ConstantsClassTrait;

		public const ADMIN = 'ROLE_ADMIN';
		public const EDITOR = 'ROLE_EDITOR';
		public const USER = 'ROLE_USER';
	}