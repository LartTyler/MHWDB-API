<?php
	namespace App\Scraping;

	use App\Console\MultiProgressBar;

	interface ProgressAwareInterface {
		/**
		 * @param MultiProgressBar $progressBar
		 *
		 * @return mixed
		 */
		public function setProgressBar(MultiProgressBar $progressBar);
	}