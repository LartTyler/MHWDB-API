<?php
	namespace App\Contrib\Management;

	class ContribGroup {
		/**
		 * @var string
		 */
		protected $rootTemplate;

		/**
		 * @var Journal
		 */
		protected $journal;

		/**
		 * ContribGroup constructor.
		 *
		 * @param string $rootTemplate
		 */
		public function __construct(string $rootTemplate) {
			$this->rootTemplate = $rootTemplate;

			if ($this->exists(Target::JSON, '.journal.json')) {
				$journalData = json_decode($this->read(Target::JSON, '/.journal.json'), true);

				if (json_last_error() !== JSON_ERROR_NONE) {
					$path = $this->getTargetRoot(Target::JSON) . '/.journal.json';

					throw new \RuntimeException('Could not parse ' . $path . ': ' . json_last_error_msg());
				}
			} else
				$journalData = [];

			$this->journal = new Journal($journalData);
		}

		/**
		 * @return string
		 */
		public function getRootTemplate(): string {
			return $this->rootTemplate;
		}

		/**
		 * @return Journal
		 */
		public function getJournal(): Journal {
			return $this->journal;
		}

		/**
		 * @param string $target
		 *
		 * @return string
		 * @see Target
		 */
		public function getTargetRoot(string $target): string {
			return str_replace(':target', $target, $this->getRootTemplate());
		}

		/**
		 * @param int         $id
		 * @param array       $data
		 * @param string|null $subgroup
		 *
		 * @return $this
		 */
		public function put(int $id, array $data, string $subgroup = null) {
			$path = $id . '.json';

			if ($subgroup)
				$path = $subgroup . '/' . $path;

			$this->journal->set($id, $path);

			$this->write(Target::JSON, $path, $this->encode($data));
			$this->write(Target::JSON, '/.journal.json', $this->encode($this->getJournal()));

			return $this;
		}

		/**
		 * @param string $uri
		 *
		 * @return $this
		 */
		public function putAsset(string $uri) {
			$path = parse_url($uri, PHP_URL_PATH);

			if ($this->exists(Target::ASSETS, $path))
				return $this;

			$this->write(Target::ASSETS, $path, file_get_contents($uri));

			return $this;
		}

		/**
		 * @param int|string $id
		 *
		 * @return object|null
		 */
		public function get($id): ?object {
			$path = $this->getPathFromJournal($id);

			if (!$path)
				return null;

			return json_decode($this->read(Target::JSON, $path));
		}

		/**
		 * @param string $uri
		 *
		 * @return string|null
		 */
		public function getAssetPath(string $uri): ?string {
			$path = $this->getTargetRoot(Target::ASSETS) . ltrim(parse_url($uri, PHP_URL_PATH), '/');

			if (!file_exists($path))
				return null;

			return $path;
		}

		/**
		 * @param int|string $id
		 *
		 * @return bool
		 */
		public function delete($id): bool {
			$path = $this->getPathFromJournal($id);

			if (!$path)
				return false;

			$this->getJournal()->delete($id);

			$this->unlink(Target::JSON, $path);
			$this->write(Target::JSON, '/.journal.json', $this->encode($this->getJournal()));

			return true;
		}

		/**
		 * @param array       $data
		 * @param string|null $subgroup
		 *
		 * @return string
		 */
		public function create(array $data, string $subgroup = null): string {
			$id = base64_encode(microtime());

			$path = $id . '.json';

			if ($subgroup)
				$path = $subgroup . '/' . $path;

			$this->getJournal()->setCreated($id, $path);

			$this->write(Target::JSON, $path, $this->encode($data));
			$this->write(Target::JSON, '/.journal.json', $this->encode($this->getJournal()));

			return $id;
		}

		/**
		 * @param int|string $id
		 *
		 * @return null|string
		 */
		protected function getPathFromJournal($id): ?string {
			$path = $this->getJournal()->get($id);

			if (!$path || !$this->exists(Target::JSON, $path))
				return null;

			return $path;
		}

		/**
		 * @param string $target
		 * @param string $path
		 * @param string $data
		 *
		 * @return $this
		 */
		protected function write(string $target, string $path, string $data) {
			$absolutePath = $this->getTargetRoot($target) . '/' . ltrim($path, '/');

			if (!file_exists($dir = dirname($absolutePath)))
				mkdir($dir, 0755, true);

			file_put_contents($absolutePath, $data);

			return $this;
		}

		/**
		 * @param string $target
		 * @param string $path
		 *
		 * @return string
		 */
		protected function read(string $target, string $path): string {
			return file_get_contents($this->getTargetRoot($target) . '/' . ltrim($path, '/'));
		}

		/**
		 * @param string $target
		 * @param string $path
		 *
		 * @return $this
		 */
		protected function unlink(string $target, string $path) {
			$path = $this->getTargetRoot($target) . '/' . ltrim($path, '/');

			if (file_exists($path))
				unlink($path);

			return $this;
		}

		/**
		 * @param string $target
		 * @param string $path
		 *
		 * @return bool
		 */
		protected function exists(string $target, string $path): bool {
			return file_exists($this->getTargetRoot($target) . '/' . ltrim($path, '/'));
		}

		/**
		 * @param \JsonSerializable|array|object $data
		 * @param bool                           $pretty
		 * @param int                            $args
		 *
		 * @return string
		 */
		protected function encode($data, bool $pretty = true, int $args = 0): string {
			$args |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

			if ($pretty)
				$args |= JSON_PRETTY_PRINT;

			$output = json_encode($data, $args);

			if ($pretty)
				$output = str_replace('    ', "\t", $output);

			return $output;
		}
	}