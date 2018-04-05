<?php
	namespace App\Console;

	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Output\OutputInterface;

	class MultiProgressBar {
		/**
		 * @var OutputInterface
		 */
		protected $output;

		/**
		 * @var ProgressBar|null
		 */
		protected $current = null;

		/**
		 * @var ProgressBar[]
		 */
		protected $stack = [];

		/**
		 * @var bool
		 */
		protected $isStarted = false;

		/**
		 * MultiProgressBar constructor.
		 *
		 * @param OutputInterface $output
		 */
		public function __construct(OutputInterface $output) {
			$this->output = $output;
		}

		/**
		 * @param int $max
		 *
		 * @return $this
		 */
		public function append(int $max = 0) {
			return $this->appendBar(new ProgressBar($this->output, $max));
		}

		/**
		 * @param ProgressBar $bar
		 *
		 * @return $this
		 */
		public function appendBar(ProgressBar $bar) {
			$this->current = $this->stack[] = $bar;

			if ($this->isStarted) {
				$this->output->writeln('');

				$bar->start();
			}

			return $this;
		}

		/**
		 * @return void
		 */
		public function start(): void {
			foreach ($this->stack as $item)
				$item->start();

			$this->isStarted = true;
		}

		/**
		 * @param int $step
		 *
		 * @return void
		 */
		public function advance(int $step = 1): void {
			if (!$this->current)
				throw new \BadMethodCallException('Cannot advance with no active bars');

			$this->current->advance($step);

			if ($this->current->getProgress() === $this->current->getMaxSteps()) {
				$this->current->finish();

				array_pop($this->stack);

				$count = sizeof($this->stack);

				if ($count > 0) {
					$this->clear();

					$this->current = $this->stack[$count - 1];
				} else
					$this->output->writeln('');
			}
		}

		/**
		 * @return void
		 */
		public function finish(): void {
			if (!$this->current)
				throw new \BadMethodCallException('Cannot finish with no active bars');

			$this->current->finish();

			array_pop($this->stack);

			$count = sizeof($this->stack);

			if ($count > 0) {
				$this->clear();

				$this->current = $this->stack[$count - 1];
			} else
				$this->output->writeln('');
		}

		/**
		 * @return void
		 */
		protected function clear(): void {
			if (!$this->isStarted)
				return;

			$this->current->clear();

			$this->output->write("\033[1A");
		}
	}