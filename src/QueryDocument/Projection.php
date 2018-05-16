<?php
	namespace App\QueryDocument;

	class Projection {
		/**
		 * @var bool[]
		 */
		protected $nodes;

		/**
		 * @var bool
		 */
		protected $default;

		/**
		 * @var ProjectionPathCache
		 */
		protected $cache;

		/**
		 * Projection constructor.
		 *
		 * @param bool[] $nodes
		 */
		protected function __construct(array $nodes) {
			$this->nodes = $nodes;
			$this->cache = new ProjectionPathCache();

			if (sizeof($nodes) === 0)
				$this->default = true;
			else {
				// If an element is not matched, the default behavior is the opposite of the value of the first element.
				// For example, for an include projection, any element not found in $nodes should be rejected (the opposite
				// of the value of the include projections, whose values are all true).

				$current = reset($nodes);

				while (is_array($current))
					$current = reset($current);

				$this->default = !(bool)$current;
			}
		}

		/**
		 * @return bool[]
		 */
		public function getNodes(): array {
			return $this->nodes;
		}

		/**
		 * @return bool
		 */
		public function isAllowedByDefault(): bool {
			return $this->default;
		}

		/**
		 * @param string $path
		 *
		 * @return bool
		 */
		public function isAllowed(string $path): bool {
			if ($this->cache->has($path))
				return $this->cache->get($path);

			$current = $this->getNodes();

			// For projections with no nodes, all paths are allowed
			if (!$current)
				return true;

			$parts = explode('.', $path);
			$result = null;

			foreach ($parts as $part) {
				if (!isset($current[$part])) {
					$result = $this->isAllowedByDefault();

					break;
				}

				$value = $current[$part];

				if (!is_array($value)) {
					$result = $value;

					break;
				}

				$current = $value;
			}

			// If $current is an array after processing all path parts, the path has child nodes and needs to be
			// allowed so that it can be processed later.
			if ($result === null && is_array($current))
				$result = true;

			return $this->cache->set($path, $result);
		}

		/**
		 * @param array       $data
		 * @param string|null $prefix
		 *
		 * @return array
		 */
		public function filter(array $data, string $prefix = null): array {
			$output = [];

			foreach ($data as $key => $value) {
				$path = ($prefix ? $prefix . '.' : '') . $key;

				if (!$this->isAllowed($path))
					continue;

				if (is_array($value) && $count = sizeof($value)) {
					if (isset($value[0])) {
						if (is_array($value[0])) {
							foreach ($value as $index => $item)
								$value[$index] = $this->filter($item, $path);
						}
					} else
						$value = $this->filter($value, $path);
				}

				$output[$key] = $value;
			}

			return $output;
		}

		/**
		 * @param array $fields
		 *
		 * @return static
		 */
		public static function fromFields(array $fields) {
			return new static(static::toNodes($fields));
		}

		/**
		 * @param array $fields
		 *
		 * @return array
		 */
		protected static function toNodes(array $fields): array {
			$nodes = [];

			foreach ($fields as $field => $value) {
				$current = &$nodes;
				$parts = explode('.', $field);

				$lastKey = array_pop($parts);

				foreach ($parts as $part) {
					if (!isset($current[$part]) || !is_array($current[$part]))
						$current[$part] = [];

					$current = &$current[$part];
				}

				$current[$lastKey] = $value;
			}

			return $nodes;
		}
	}