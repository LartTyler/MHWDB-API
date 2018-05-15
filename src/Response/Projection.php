<?php
	namespace App\Response;

	class Projection {
		/**
		 * @var bool[]
		 */
		protected $fields;

		/**
		 * @var bool
		 */
		protected $include;

		/**
		 * Projection constructor.
		 *
		 * @param bool[] $fields
		 */
		public function __construct(array $fields) {
			$this->fields = $fields;
			$this->include = reset($fields);
		}

		/**
		 * @return bool
		 */
		public function isInclude(): bool {
			return $this->include;
		}

		/**
		 * @param string $path
		 *
		 * @return bool
		 */
		public function isAllowed(string $path): bool {
			if (!$this->fields)
				return true;

			$found = $this->fields[$path] ?? false;

			return $this->isInclude() && $found || !$found;
		}

		/**
		 * @param array       $data
		 * @param string|null $prefix
		 *
		 * @return array
		 */
		public function filter(array &$data, string $prefix = null): array {
			if (!$this->fields)
				return $data;

			foreach ($data as $key => $value) {
				$path = ($prefix ? $prefix . '.' : '') . $key;

				if (!$this->isAllowed($path)) {
					unset($data[$key]);

					continue;
				} else if (is_array($value))
					$this->filter($value, $path);
			}

			return $data;
		}
	}