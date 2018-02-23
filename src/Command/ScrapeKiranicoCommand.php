<?php
	namespace App\Command;

	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use Doctrine\Bundle\DoctrineBundle\Registry;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;

	class ScrapeKiranicoCommand extends Command {
		/**
		 * @var KiranicoScrapeTarget
		 */
		protected $target;

		/**
		 * @var \Doctrine\Common\Persistence\ObjectManager|object
		 */
		protected $manager;

		/**
		 * ScrapeKiranicoCommand constructor.
		 *
		 * @param KiranicoScrapeTarget $target
		 * @param Registry             $doctrine
		 */
		public function __construct(KiranicoScrapeTarget $target, Registry $doctrine) {
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