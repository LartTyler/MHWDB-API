<?php
	namespace App\Loaders;

	class LoaderCollection implements \IteratorAggregate {
		/**
		 * @var LoaderInterface[]
		 */
		protected $loaders;

		/**
		 * LoaderCollection constructor.
		 *
		 * @param LoaderInterface[] $loaders
		 */
		public function __construct(array $loaders) {
			$this->setLoaders($loaders);
		}

		/**
		 * @param array $keys
		 *
		 * @return LoaderInterface[]
		 */
		public function getLoaders(array $keys = []): array {
			if (!$keys)
				return $this->loaders;

			return array_filter($this->loaders, function(LoaderInterface $loader) use ($keys): bool {
				return in_array($loader->getType(), $keys);
			}, $this->loaders);
		}

		/**
		 * @param array $loaders
		 *
		 * @return $this
		 */
		public function setLoaders(array $loaders) {
			$this->loaders = [];

			foreach ($loaders as $loader)
				$this->addLoader($loader);

			return $this;
		}

		/**
		 * @param string $key
		 *
		 * @return LoaderInterface|null
		 */
		public function getLoader(string $key): ?LoaderInterface {
			return $this->loaders[$key] ?? null;
		}

		/**
		 * @param LoaderInterface $loader
		 *
		 * @return $this
		 */
		public function addLoader(LoaderInterface $loader) {
			$this->loaders[$loader->getType()] = $loader;

			return $this;
		}

		/**
		 * @return int
		 */
		public function count(): int {
			return sizeof($this->loaders);
		}

		public function getIterator(): \ArrayIterator {
			return new \ArrayIterator($this->loaders);
		}
	}