<?php
	namespace App\Game;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	/**
	 * Longest value: NIGHT = 5
	 */
	final class EndemicLifeSpawnCondition {
		use ConstantsClassTrait;

		public const RAIN = 'rain';
		public const NIGHT = 'night';
	}