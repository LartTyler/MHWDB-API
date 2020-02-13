<?php
	namespace App\Game;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	/**
	 * Longest value: UNCLASSIFIED = 12
	 */
	final class EndemicLifeType {
		use ConstantsClassTrait;

		public const AIRBORNE = 'airborne';
		public const AQUATIC = 'aquatic';
		public const EVENT = 'event';
		public const TERRESTRIAL = 'terrestrial';
		public const TREETOP = 'treetop';
		public const UNCLASSIFIED = 'unclassified';
	}