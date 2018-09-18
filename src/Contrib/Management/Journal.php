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

			if (!isset($this->data['created']))
				$this->data['created'] = [];

			if (!isset($this->data['deleted']))
				$this->data['deleted'] = [];
		}

		/**
		 * @param string|int $id
		 * @param string     $path
		 *
		 * @return $this
		 */
		public function set($id, string $path) {
			if ($this->get($id) === $path)
				return $this;

			if (isset($this->data['created'][$id]))
				$this->data['created'][$id] = $path;
			else
				$this->data[(string)$id] = $path;

			$this->dirty = true;

			return $this;
		}

		/**
		 * @param int|string $id
		 *
		 * @return null|string
		 */
		public function get($id): ?string {
			if (isset($this->data['created'][$id]))
				return $this->data['created'][$id];

			return $this->data[(string)$id] ?? null;
		}

		/**
		 * @param int|string $id
		 *
		 * @return $this
		 */
		public function delete($id) {
			if (isset($this->data['created'][$id]))
				unset($this->data['created'][$id]);
			else {
				unset($this->data[(string)$id]);

				$this->data['deleted'][] = $id;
			}

			return $this;
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