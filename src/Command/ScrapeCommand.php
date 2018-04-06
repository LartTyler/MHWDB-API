<?php
	namespace App\Command;

	use App\Console\MultiProgressBar;
	use App\Scraping\ProgressAwareInterface;
	use App\Scraping\ScraperCollection;
	use App\Scraping\ScraperInterface;
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
				->addOption('type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
				->addOption('context', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$types = $input->getOption('type');
			$contexts = [];

			foreach ($input->getOption('context') as $value) {
				$type = strtok($value, ':');
				$key = strtok(':');
				$value = strtok('');

				if (!$value)
					$value = [true];
				else
					$value = array_filter(array_map(function(string $item): string {
						return trim($item);
					}, explode(',', $value)));

				if (!isset($contexts[$type]))
					$contexts[$type] = [];

				$contexts[$type][$key] = $value;
			}

			$progress = new MultiProgressBar($output);
			$progress->append($types ? sizeof($types) : $this->scrapers->count());

			$progress->start();

			foreach ($this->scrapers as $scraper) {
				if ($types && !in_array($scraper->getType(), $types))
					continue;

				$context = $contexts[$scraper->getType()] ?? [];

				if ($scraper instanceof ProgressAwareInterface)
					$scraper->setProgressBar($progress);

				$scraper->scrape($context);

				$progress->advance();
			}

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}