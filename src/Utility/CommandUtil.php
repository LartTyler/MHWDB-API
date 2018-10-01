<?php
	namespace App\Utility;

	final class CommandUtil {
		/**
		 * CommandUtil constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param string $command
		 * @param mixed  ...$args
		 *
		 * @return string
		 */
		public static function exec(string $command, ...$args): string {
			return exec(sprintf($command, ...array_map(function($arg) {
				return escapeshellarg($arg);
			}, $args)));
		}
	}