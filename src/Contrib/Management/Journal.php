<?php declare(strict_types = 1);
	namespace App\Contrib\Management;

	class Journal implements \JsonSerializable {
		/**
		 * @var string[]
		 */
		protected $data;

		/**
		 * @var bool
		 */
		protected $dirty = true;

		/**
		 * Journal constructor.
		 *
		 * @param string[] $data
		 */
		public function __construct(array $data) {
			$this->data = $data;
		}

		/**
		 * @param int $id
		 * @param string     $path
		 *
		 * @return $this
		 */
		public function set(int $id, string $path) {
			if ($this->get($id) === $path)
				return $this;

			$this->data[(string)$id] = $path;
			$this->dirty = true;

			return $this;
		}

		/**
		 * @param int $id
		 *
		 * @return null|string
		 */
		public function get(int $id): ?string {
			return $this->data[(string)$id] ?? null;
		}

		/**
		 * @param int $id
		 *
		 * @return $this
		 */
		public function delete(int $id) {
			unset($this->data[(string)$id]);

			return $this;
		}

		/**
		 * @param string $id
		 * @param string $path
		 *
		 * @return $this
		 * @deprecated Support for deferred creation is being removed
		 */
		public function setCreated(string $id, string $path) {
			$this->data['created'][$id] = $path;

			return $this;
		}

		/**
		 * @return array
		 * @deprecated Support for deferred creation is being removed
		 */
		public function getCreated(): array {
			return $this->data['created'];
		}

		/**
		 * @return $this
		 * @deprecated Support for deferred creation is being removed
		 */
		public function clearCreated() {
			$this->data['created'] = [];

			return $this;
		}

		/**
		 * @return array
		 * @deprecated Support for deferred deletion is being removed
		 */
		public function getDeleted(): array {
			return $this->data['deleted'];
		}

		/**
		 * @return $this
		 * @deprecated Support for deferred deletion is being removed
		 */
		public function clearDeleted() {
			$this->data['deleted'] = [];

			return $this;
		}

		/**
		 * @return string[]
		 */
		public function all(): array {
			if ($this->dirty) {
				ksort($this->data);

				$this->dirty = false;
			}

			return $this->data;
		}

		/**
		 * @return array
		 */
		public function jsonSerialize(): array {
			return $this->all();
		}
	}