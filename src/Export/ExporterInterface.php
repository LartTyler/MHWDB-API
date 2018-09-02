<?php
	namespace App\Export;

	interface ExporterInterface {
		/**
		 * @return string
		 */
		public function getSupportedClass(): string;

		/**
		 * @param object $object
		 *
		 * @return Export
		 */
		public function export(object $object): Export;
	}