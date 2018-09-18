<?php
	namespace App\Command;

	use App\Contrib\Management\ContribManager;
	use App\Utility\CommandUtil;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;

	class ContribPushCommand extends Command {
		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * ContribPushCommand constructor.
		 *
		 * @param ContribManager $contribManager
		 */
		public function __construct(ContribManager $contribManager) {
			parent::__construct();

			$this->contribManager = $contribManager;
		}

		/**
		 * @return void
		 */
		protected function configure(): void {
			$this
				->setName('app:contrib:push')
				->addOption('rebase', 'r', InputOption::VALUE_NONE,
					'If set, the push will be run with the --rebase flag');
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$command = 'git -C %s push';

			if ($input->getOption('rebase'))
				$command .= ' --rebase';

			CommandUtil::exec($command, $this->contribManager->getContribDir());
		}
	}