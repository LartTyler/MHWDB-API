<?php
	namespace App\Contrib\Management;

	class ContribManager {
		/**
		 * @var string
		 */
		protected $contribDir;

		/**
		 * @var ContribGroup[]
		 */
		protected $groups = [];

		/**
		 * ContribManager constructor.
		 *
		 * @param string $contribDir
		 */
		public function __construct(string $contribDir) {
			$this->contribDir = $contribDir;
		}

		/**
		 * @param string $type
		 *
		 * @return ContribGroup
		 */
		public function getGroup(string $type): ContribGroup {
			if (isset($this->groups[$type]))
				return $this->groups[$type];

			return $this->groups[$type] = new ContribGroup($this->contribDir . '/:target/' . $type);
		}
	}