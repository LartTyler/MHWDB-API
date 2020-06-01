<?php
	namespace App\Game\Quest;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	/**
	 * Longest value: CAPTURE = DELIVER = 7
	 */
	final class QuestObjective {
		use ConstantsClassTrait;

		public const CAPTURE = 'capture';
		public const DELIVER = 'deliver';
		public const GATHER = 'gather';
		public const HUNT = 'hunt';
		public const SLAY = 'slay';

		public const MONSTER_OBJECTIVES = [
			self::CAPTURE,
			self::HUNT,
			self::SLAY,
		];
	}