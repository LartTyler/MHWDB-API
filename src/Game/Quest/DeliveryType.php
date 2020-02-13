<?php
	namespace App\Game\Quest;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	/**
	 * Longest value: ENDEMIC_LIFE = 12
	 */
	final class DeliveryType {
		use ConstantsClassTrait;

		public const ENDEMIC_LIFE = 'endemic life';
		public const OBJECT = 'object';
	}