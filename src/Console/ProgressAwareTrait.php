<?php
	namespace App\Console;

	trait ProgressAwareTrait {
		/**
		 * @var MultiProgressBar|null
		 */
		protected $progressBar = null;

		/**
		 * @param MultiProgressBar $progressBar
		 *
		 * @return void
		 */
		public function setProgressBar(MultiProgressBar $progressBar): void {
			$this->progressBar = $progressBar;
		}
	}