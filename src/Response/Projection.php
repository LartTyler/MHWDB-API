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
		 * @var bool[]
		 */
		protected $parents = [];

		/**
		 * Projection constructor.
		 *
		 * @param bool[] $fields
		 */
		public function __construct(array $fields) {
			$this->fields = $fields;
			$this->include = reset($fields);

			// Also append the parent node of any child fields
			foreach (array_keys($fields) as $field) {
				$pos = strrpos($field, '.');

				if ($pos === false)
					continue;

				$this->parents[substr($field, 0, $pos)] = true;
			}
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

			return $this->isInclude() ? $found : !$found;
		}

		/**
		 * @param string $path
		 *
		 * @return bool
		 */
		public function isParent(string $path): bool {
			return isset($this->parents[$path]);
		}

		/**
		 * @param array       $data
		 * @param string|null $prefix
		 *
		 * @return array
		 */
		public function filter(array $data, string $prefix = null): array {
			if (!$this->fields)
				return $data;

			$output = [];

			// TODO This whole thing works, but needs to be refined
			foreach ($data as $key => $value) {
				$path = ($prefix ? $prefix . '.' : '') . $key;

				if (!$this->isAllowed($path)) {
					if ($this->isParent($path)) {
						if (is_array($value)) {
							$count = sizeof($value);

							if ($count > 0 && isset($value[0])) {
								foreach ($value as $index => $item)
									$value[$index] = $this->filter($item, $path);
							} else if ($count > 0) {
								foreach ($value as $childKey => $item) {
									if (!$this->isAllowed($path . '.' . $childKey))
										continue 2;
								}
							}
						} else
							continue;
					} else
						continue;
				}

				$output[$key] = $value;
			}

			return $output;
		}
	}