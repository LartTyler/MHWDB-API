<?php
	namespace App\Game\Quest;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	final class Objective {
		use ConstantsClassTrait;

		public const HUNT = 'hunt';
		public const SLAY = 'slay';
		public const CAPTURE = 'capture';
		public const GATHER = 'gather';
	}