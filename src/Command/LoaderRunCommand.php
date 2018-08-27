<?php
	namespace App\Command;

	use App\Console\MultiProgressBar;
	use App\Loaders\LoaderCollection;
	use App\Loaders\LoaderInterface;
	use App\Scraping\ProgressAwareInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class LoaderRunCommand extends Command {
		/**
		 * @var LoaderCollection
		 */
		protected $loaders;

		/**
		 * LoadCommand constructor.
		 *
		 * @param LoaderCollection $loaders
		 */
		public function __construct(LoaderCollection $loaders) {
			parent::__construct();

			$this->loaders = $loaders;
		}

		/**
		 * @return void
		 */
		protected function configure(): void {
			$this
				->setName('app:loaders:run')
				->addOption('type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
				->addOption('context', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
				->addOption('begin-at-type', null, InputOption::VALUE_REQUIRED)
				->addOption('exclude-type', 'x', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$io = new SymfonyStyle($input, $output);
			$contexts = [];

			foreach ($input->getOption('context') as $value) {
				$type = strtok($value, ':');
				$key = strtok(':');
				$value = strtok('');

				if (!$value)
					$value = [true];
				else {
					$value = array_filter(array_map(function(string $item): string {
						return trim($item);
					}, explode(',', $value)));
				}

				if (!isset($contexts[$type]))
					$contexts[$type] = [];

				$contexts[$type][$key] = $value;
			}

			/** @var LoaderInterface[] $loaders */
			$loaders = array_values($this->loaders->getLoaders());

			if ($beginAt = $input->getOption('begin-at-type')) {
				$index = null;

				foreach ($loaders as $i => $loader) {
					if ($loader->getType() === $beginAt) {
						$index = $i;

						break;
					}
				}

				if ($index === null)
					throw new \InvalidArgumentException('The string "' . $beginAt . '" is not a recognized type');

				$loaders = array_slice($loaders, $index);
			} else if ($types = $input->getOption('type')) {
				$loaders = array_filter($loaders, function(LoaderInterface $loader) use ($types): bool {
					return in_array($loader->getType(), $types);
				});
			}

			if ($exclude = $input->getOption('exclude-type')) {
				$loaders = array_filter($loaders, function(LoaderInterface $loader) use ($exclude): bool {
					return !in_array($loader->getType(), $exclude);
				});
			}

			if (sizeof($loaders) === $this->loaders->count())
				$io->comment('Running loaders: all');
			else {
				$io->comment(
					'Running loaders: ' .implode(', ', array_map(function(LoaderInterface $loader): string {
						return $loader->getType();
					}, $loaders))
				);
			}

			$progress = new MultiProgressBar($output);
			$progress->append(sizeof($loaders));

			$progress->start();

			foreach ($loaders as $loader) {
				$context = $contexts[$loader->getType()] ?? [];

				if ($loader instanceof ProgressAwareInterface)
					$loader->setProgressBar($progress);

				$loader->load($context);

				$progress->advance();
			}

			$io->success('Done!');
		}
	}