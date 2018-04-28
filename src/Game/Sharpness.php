<?php
	namespace App\Game;

	final class Sharpness {
		const RED = 'red';
		const ORANGE = 'orange';
		const YELLOW = 'yellow';
		const GREEN = 'green';
		const BLUE = 'blue';

		const ALL = [
			self::RED,
			self::ORANGE,
			self::YELLOW,
			self::GREEN,
			self::BLUE,
		];

		/**
		 * Sharpness constructor.
		 */
		private function __construct() {
		}
	}