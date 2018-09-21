<?php
	namespace App\Contrib\Management;

	use App\Utility\CommandUtil;

	class ContribManager {
		/**
		 * @var string
		 */
		protected $contribDir;

		/**
		 * @var bool
		 */
		protected $allowCommits = true;

		/**
		 * @var bool
		 */
		protected $allowPush = true;

		/**
		 * @var ContribGroup[]
		 */
		protected $groups = [];

		/**
		 * ContribManager constructor.
		 *
		 * @param string $contribDir
		 */
		public function __construct(string $contribDir) {
			$this->contribDir = $contribDir;
		}

		/**
		 * @return bool
		 */
		public function getAllowCommits(): bool {
			return $this->allowCommits;
		}

		/**
		 * @param bool $allowCommits
		 *
		 * @return $this
		 */
		public function setAllowCommits(bool $allowCommits) {
			$this->allowCommits = $allowCommits;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function getAllowPush(): bool {
			return $this->allowPush;
		}

		/**
		 * @param bool $allowPush
		 *
		 * @return $this
		 */
		public function setAllowPush(bool $allowPush) {
			$this->allowPush = $allowPush;

			return $this;
		}

		/**
		 * @return ContribGroup[]
		 */
		public function getGroups(): array {
			return $this->groups;
		}

		/**
		 * @param ContribGroup[] $groups
		 *
		 * @return $this
		 */
		public function setGroups(array $groups) {
			$this->groups = $groups;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getContribDir(): string {
			return $this->contribDir;
		}

		/**
		 * @param string $type
		 *
		 * @return ContribGroup
		 */
		public function getGroup(string $type): ContribGroup {
			if (isset($this->groups[$type]))
				return $this->groups[$type];

			return $this->groups[$type] = new ContribGroup($this, $this->contribDir . '/:target/' . $type);
		}

		/**
		 * @param array $targets
		 *
		 * @return void
		 */
		public function clean(array $targets = []): void {
			if (!$targets)
				$targets = [Target::ASSETS, Target::JSON];

			foreach ($targets as $target)
				exec(sprintf('rm -rf %s', escapeshellarg($this->contribDir . '/' . $target)));
		}

		/**
		 * @param string   $message
		 * @param string[] $paths an array of paths that changed, where the key is the path, and the value is the
		 *                        operation to pass the path to (i.e. "add" or "rm")
		 *
		 * @return void
		 */
		public function commit(string $message, array $paths = []): void {
			if (!$this->getAllowCommits())
				return;

			if (!trim(CommandUtil::exec('git -C %s status -s', $this->contribDir)))
				return;

			if (!$paths)
				CommandUtil::exec('git -C %s add .', $this->contribDir);
			else {
				foreach ($paths as $path => $action)
					CommandUtil::exec('git -C %s %s %s', $this->contribDir, $action, $path);
			}

			CommandUtil::exec('git -C %s commit -m %s', $this->contribDir, $message);
		}

		/**
		 * @return void
		 */
		public function push(): void {
			if (!$this->getAllowPush())
				return;

			CommandUtil::exec('git -C %s push', $this->contribDir);
		}
	}