<?php
	namespace App\Command;

	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
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
				->setName('scrape:kiranico');
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			$scrapers = $this->target->getScrapers();

			$io->progressStart(sizeof($scrapers));

			foreach ($scrapers as $scraper) {
				$scraper->scrape();

				$this->manager->flush();

				$io->progressAdvance();
			}

			$this->target->scrape();
			$this->manager->flush();

			$io->progressFinish();
			$io->success('Done!');
		}
	}