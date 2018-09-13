<?php
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

			if (!isset($this->data['created']))
				$this->data['created'] = [];

			if (!isset($this->data['deleted']))
				$this->data['deleted'] = [];
		}

		/**
		 * @param int    $id
		 * @param string $path
		 *
		 * @return $this
		 */
		public function set(int $id, string $path) {
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

			$this->data['deleted'][] = $id;

			return $this;
		}

		/**
		 * @param int $id
		 *
		 * @return bool
		 */
		public function has(int $id): bool {
			return $this->get($id) !== null;
		}

		/**
		 * @param string $id
		 * @param string $path
		 *
		 * @return $this
		 */
		public function setCreated(string $id, string $path) {
			$this->data['created'][$id] = $path;

			return $this;
		}

		/**
		 * @return array
		 */
		public function getCreated(): array {
			return $this->data['created'];
		}

		/**
		 * @return $this
		 */
		public function clearCreated() {
			$this->data['created'] = [];

			return $this;
		}

		/**
		 * @return array
		 */
		public function getDeleted(): array {
			return $this->data['deleted'];
		}

		/**
		 * @return $this
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

			$data = $this->data;

			if (!$data['created'])
				$data['created'] = new \stdClass();

			return $data;
		}

		/**
		 * @return array
		 */
		public function jsonSerialize(): array {
			return $this->all();
		}
	}