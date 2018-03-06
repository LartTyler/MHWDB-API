<?php
	namespace App\Search;

	class FieldInfo {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var bool
		 */
		protected $json;

		/**
		 * FieldInfo constructor.
		 *
		 * @param string $name
		 * @param bool   $json
		 */
		public function __construct(string $name, bool $json = false) {
			$this->name = $name;
			$this->json = $json;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return bool
		 */
		public function isJson(): bool {
			return $this->json;
		}
	}