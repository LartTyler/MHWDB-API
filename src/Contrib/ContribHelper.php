<?php
	namespace App\Contrib;

	class ContribHelper {
		/**
		 * @var string
		 */
		protected $contribDir;

		/**
		 * An array of any loaded journals. To avoid excessive IO / decoding, journals are kept in memory unless
		 * explicitly cleared by calling {@see ContribHelper::clearJournal()} or {@see ContribHelper::clearJournals()}.
		 *
		 * @var array
		 */
		protected $journals = [];

		/**
		 * ContribHelper constructor.
		 *
		 * @param string $contribDir
		 */
		public function __construct(string $contribDir) {
			$this->contribDir = $contribDir;
		}

		/**
		 * @return string
		 */
		public function getContribDir(): string {
			return $this->contribDir;
		}

		/**
		 * @param string $type
		 * @param string $id
		 * @param string $target
		 *
		 * @return string
		 */
		public function getContribPath(string $type, $id, string $target = 'json'): ?string {
			$basePath = $this->getContribDir() . '/' . $target . '/' . $type;

			if (!isset($this->journals[$type])) {
				$decoded = json_decode(file_get_contents($basePath . '/.journal.json'), true, 512, JSON_THROW_ON_ERROR);

				$this->journals[$type] = $decoded;
			}

			$path = $this->journals[$type][$id] ?? null;

			if (!$path)
				return null;

			return $basePath . '/' . $path;
		}

		/**
		 * @param string $type
		 *
		 * @return void
		 */
		public function clearJournal(string $type): void {
			unset($this->journals[$type]);
		}

		/**
		 * @return void
		 */
		public function clearJournals(): void {
			$this->journals = [];
		}
	}