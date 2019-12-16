<?php
	namespace App\Game;

	final class WorldEventType {
		public const KULVE_TAROTH = 'kulve taroth siege';
		public const SAFI_JIIVA = 'safi\'jiiva siege';
		public const EVENT_QUEST = 'event quest';
		public const CHALLENGE_QUEST = 'challenge quest';

		/**
		 * @var string[]|null
		 */
		private static $types = null;

		/**
		 * WorldEventType constructor.
		 */
		private function __construct() {
		}

		/**
		 * @return string[]
		 */
		public static function all(): array {
			if (self::$types === null)
				self::$types = (new \ReflectionClass(self::class))->getConstants();

			return self::$types;
		}
	}