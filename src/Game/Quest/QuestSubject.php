<?php
	namespace App\Game\Quest;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	/**
	 * Longest value: MONSTER = 7
	 */
	final class QuestSubject {
		use ConstantsClassTrait;

		public const ENTITY = 'entity';
		public const ITEM = 'item';
		public const MONSTER = 'monster';
	}