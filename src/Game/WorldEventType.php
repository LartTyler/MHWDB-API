<?php
	namespace App\Game;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	final class WorldEventType {
		use ConstantsClassTrait;

		public const KULVE_TAROTH = 'kulve taroth siege';
		public const SAFI_JIIVA = 'safi\'jiiva siege';
		public const EVENT_QUEST = 'event quest';
		public const CHALLENGE_QUEST = 'challenge quest';
	}