<?php
	namespace App\Console;

	interface ProgressAwareInterface {
		/**
		 * @param MultiProgressBar $progressBar
		 *
		 * @return mixed
		 */
		public function setProgressBar(MultiProgressBar $progressBar);
	}