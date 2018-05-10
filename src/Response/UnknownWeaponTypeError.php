<?php
	namespace App\Response;

	use DaybreakStudios\Doze\Errors\ApiError;

	class UnknownWeaponTypeError extends ApiError {
		/**
		 * UnknownWeaponType constructor.
		 *
		 * @param string $type
		 */
		public function __construct(string $type) {
			parent::__construct('game.unknown_weapon_type', $type . ' is not a recognized weapon type');
		}
	}