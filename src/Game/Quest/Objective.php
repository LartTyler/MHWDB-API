<?php
	namespace App\Game\Quest;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	final class Objective {
		use ConstantsClassTrait;

		public const CAPTURE = 'capture';
		public const DELIVER = 'deliver';
		public const GATHER = 'gather';
		public const HUNT = 'hunt';
		public const SLAY = 'slay';
	}