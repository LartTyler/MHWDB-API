<?php
	namespace App\Game\Quest;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	/**
	 * Longest value: SPECIAL_ASSIGNMENT = 18
	 */
	final class QuestType {
		use ConstantsClassTrait;

		public const ARENA = 'arena';
		public const ASSIGNMENT = 'assignment';
		public const CHALLENGE = 'challenge';
		public const EVENT = 'event';
		public const OPTIONAL = 'optional';
		public const SPECIAL_ASSIGNMENT = 'special assignment';
	}