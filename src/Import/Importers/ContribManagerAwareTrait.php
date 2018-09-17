<?php
	namespace App\Import\Importers;

	use App\Contrib\Management\ContribManager;

	trait ContribManagerAwareTrait {
		/**
		 * @var ContribManager|null
		 */
		protected $contribManager = null;

		/**
		 * @required
		 *
		 * @param ContribManager $contribManager
		 */
		public function setContribManager(ContribManager $contribManager) {
			$this->contribManager = $contribManager;
		}
	}