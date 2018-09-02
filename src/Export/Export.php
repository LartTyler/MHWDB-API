<?php
	namespace App\Export;

	class Export {
		/**
		 * @var array
		 */
		protected $data;

		/**
		 * @var string
		 */
		protected $group;

		/**
		 * Export constructor.
		 *
		 * @param string $group
		 * @param array  $data
		 */
		public function __construct(string $group, array $data) {
			$this->group = $group;
			$this->data = $data;
		}

		/**
		 * @return array
		 */
		public function getData(): array {
			return $this->data;
		}

		/**
		 * @return string
		 */
		public function getGroup(): string {
			return $this->group;
		}
	}