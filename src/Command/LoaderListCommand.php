<?php
	namespace App\Command;

	use App\Loaders\LoaderCollection;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class LoaderListCommand extends Command {
		/**
		 * @var string[]
		 */
		protected $loaderKeys;

		/**
		 * LoaderListCommand constructor.
		 *
		 * @param LoaderCollection $loaders
		 */
		public function __construct(LoaderCollection $loaders) {
			parent::__construct();

			$this->loaderKeys = array_keys($loaders->getLoaders());
		}

		/**
		 * @return void
		 */
		protected function configure() {
			$this->setName('app:loaders:list');
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return int|null|void
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			$types = [];

			foreach ($this->loaderKeys as $index => $type)
				$types[] = ($index + 1) . ': ' . $type;

			$io->section('Loader Types (in execution order)');
			$io->listing($types);
		}
	}