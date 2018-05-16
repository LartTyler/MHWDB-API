<?php
	namespace App\QueryDocument;

	class ProjectionPathCache {
		protected $data = [];

		/**
		 * @param string $path
		 * @param bool   $value
		 *
		 * @return bool
		 */
		public function set(string $path, bool $value): bool {
			return $this->data[$path] = $value;
		}

		/**
		 * @param string $path
		 *
		 * @return bool
		 */
		public function has(string $path): bool {
			return isset($this->data[$path]);
		}

		/**
		 * @param string $path
		 *
		 * @return bool
		 */
		public function get(string $path): bool {
			if (!$this->has($path)) {
				throw new \InvalidArgumentException($path . ' does not exist in the cache. Use ' . static::class .
					'::has() to check first!');
			}

			return $this->data[$path];
		}
	}