<?php
	namespace App\Entity;

	interface LengthCachingEntityInterface {
		/**
		 * @return void
		 */
		public function syncLengthFields(): void;
	}