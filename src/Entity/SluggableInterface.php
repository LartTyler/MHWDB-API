<?php
	namespace App\Entity;

	interface SluggableInterface {
		/**
		 * @return string
		 */
		public function getSlug(): string;
	}