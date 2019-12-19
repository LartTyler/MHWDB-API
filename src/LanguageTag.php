<?php
	namespace App;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	/**
	 * Longest value: CHINESE_TRADITIONAL = 7
	 */
	final class LanguageTag {
		use ConstantsClassTrait;

		public const ENGLISH = 'en';
		public const FRENCH = 'fr';
		public const GERMAN = 'de';
		public const CHINESE_SIMPLIFIED = 'zh';
		public const CHINESE_TRADITIONAL = 'zh-Hant';
	}