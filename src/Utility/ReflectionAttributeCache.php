<?php
	namespace App\Utility;

	class ReflectionAttributeCache {
		protected $cache = [];

		/**
		 * @param object            $object
		 * @param string            $attribute
		 * @param \ReflectionMethod $accessor
		 *
		 * @return void
		 */
		public function set($object, string $attribute, \ReflectionMethod $accessor): void {
			$class = get_class($object);

			if (!isset($this->cache[$class]))
				$this->cache[$class] = [];

			$this->cache[$class][$attribute] = $accessor;
		}

		/**
		 * @param object              $object
		 * @param \ReflectionMethod[] $attributes
		 *
		 * @return void
		 */
		public function setAll($object, array $attributes): void {
			foreach ($attributes as $attribute => $accessor)
				$this->set($object, $attribute, $accessor);
		}

		/**
		 * @param object $object
		 * @param string $attribute
		 *
		 * @return null|\ReflectionMethod
		 */
		public function get($object, string $attribute): ?\ReflectionMethod {
			return $this->cache[get_class($object)][$attribute] ?? null;
		}

		/**
		 * @param object $object
		 *
		 * @return array|null
		 */
		public function getAll($object): ?array {
			return $this->cache[get_class($object)] ?? null;
		}

		/**
		 * @param object $object
		 * @param string $attribute
		 *
		 * @return void
		 */
		public function remove($object, ?string $attribute = null): void {
			$class = get_class($object);

			if (!isset($this->cache[$class]))
				return;

			if ($attribute !== null)
				unset($this->cache[$class][$attribute]);
			else
				unset($this->cache[$class]);
		}
	}