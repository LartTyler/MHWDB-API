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

			if (!$this->isValid($fields, $this->include))
				throw new \InvalidArgumentException('You cannot mix includes and excludes in a projection');

			// Also append the parent node of any child fields
			$this->setParentsFromFields($fields);
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

			return $this->fields[$path] ?? !$this->isInclude();
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
		 * @param string $path
		 *
		 * @return bool
		 */
		public function isAllowedOrParent(string $path): bool {
			return $this->isAllowed($path) || $this->isParent($path);
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

		/**
		 * @param array $fields
		 * @param bool  $isInclude
		 *
		 * @return bool
		 */
		protected function isValid(array $fields, bool $isInclude): bool {
			foreach ($fields as $value) {
				if ($value !== $isInclude)
					return false;
			}

			return true;
		}

		/**
		 * @param bool[] $fields
		 *
		 * @return $this
		 */
		protected function setParentsFromFields(array $fields) {
			foreach (array_keys($fields) as $field) {
				$pos = strrpos($field, '.');

				if ($pos === false)
					continue;

				$this->parents[substr($field, 0, $pos)] = true;
			}

			return $this;
		}
	}