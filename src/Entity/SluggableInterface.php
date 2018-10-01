<?php
	namespace App\Entity;

	/**
	 * @package App\Entity
	 * @deprecated
	 */
	interface SluggableInterface {
		/**
		 * @return string
		 */
		public function getSlug(): string;
	}