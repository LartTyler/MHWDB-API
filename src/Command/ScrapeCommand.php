<?php
	namespace App\Command;

	use App\Console\MultiProgressBar;
	use App\Scraper\ScraperInterface;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ScraperCollection;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ScrapeCommand extends Command {
		/**
		 * @var ScraperCollection|ScraperInterface[]
		 */
		protected $scrapers;

		/**
		 * ScrapeCommand constructor.
		 *
		 * @param ScraperCollection|ScraperInterface[] $scrapers
		 */
		public function __construct(ScraperCollection $scrapers) {
			parent::__construct();

			$this->scrapers = $scrapers;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function configure(): void {
			$this
				->setName('app:scrape')
				->addOption('type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$types = $input->getOption('type');

			$progress = new MultiProgressBar($output);
			$progress->append($types ? sizeof($types) : $this->scrapers->count());

			$progress->start();

			foreach ($this->scrapers as $scraper) {
				if ($types && !in_array($scraper->getType(), $types))
					continue;

				if ($scraper instanceof ProgressAwareInterface)
					$scraper->setProgressBar($progress);

				$scraper->scrape();

				$progress->advance();
			}

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}