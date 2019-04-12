<?php
	namespace App\Loaders;

	interface SchemaInterface {
		/**
		 * @param array $data
		 *
		 * @return static
		 */
		public static function create(array $data);
	}