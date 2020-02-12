<?php
	namespace App\Game;

	use DaybreakStudios\RestApiCommon\Utility\ConstantsClassTrait;

	/**
	 * Longest value: MASTER = 6
	 */
	final class Rank {
		use ConstantsClassTrait;

		const LOW = 'low';
		const HIGH = 'high';
		const MASTER = 'master';
	}