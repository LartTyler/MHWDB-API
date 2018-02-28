<?php
	namespace App\Command;

	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ScrapeKiranicoCommand extends Command {
		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var EntityManager
		 */
		protected $manager;

		/**
		 * ScrapeKiranicoCommand constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param RegistryInterface    $doctrine
		 */
		public function __construct(KiranicoScrapeTarget $target, RegistryInterface $doctrine) {
			parent::__construct();

			$this->target = $target;
			$this->manager = $doctrine->getManager();
		}

		/**
		 * {@inheritdoc}
		 */
		protected function configure() {
			$this
				->setName('scrape:kiranico')
				->addOption('type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			$scrapers = $this->target->getScrapers();

			$types = $input->getOption('type');

			$io->progressStart($types ? sizeof($types) : sizeof($scrapers));

			foreach ($scrapers as $i => $scraper) {
				if ($types && !in_array($scraper->getType(), $types))
					continue;

				$scraper->scrape();

				$this->manager->flush();

				// We sleep to avoid hitting the scrape target too rapidly
				sleep(5);

				$io->progressAdvance();
			}

			$this->manager->flush();

			$io->progressFinish();
			$io->success('Done!');
		}
	}