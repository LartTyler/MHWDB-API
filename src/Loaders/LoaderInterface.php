<?php
	namespace App\Loaders;

	interface LoaderInterface {
		/**
		 * @return string
		 */
		public function getType(): string;

		/**
		 * @param array $context
		 *
		 * @return void
		 */
		public function load(array $context): void;
	}