<?php
	namespace App\Command;

	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use Doctrine\ORM\EntityManager;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;

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
			$this->target->scrape();

			$this->manager->flush();

			$output->writeln('Done!');
		}
	}