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
				->addOption('context', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
				->addOption('type-begin-at', null, InputOption::VALUE_REQUIRED);
		}

		/**
		 * {@inheritdoc}
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
				else
					$value = array_filter(array_map(function(string $item): string {
						return trim($item);
					}, explode(',', $value)));

				if (!isset($contexts[$type]))
					$contexts[$type] = [];

				$contexts[$type][$key] = $value;
			}

			/** @var ScraperInterface[] $scrapers */
			$scrapers = array_values($this->scrapers->getScrapers());

			if ($beginAt = $input->getOption('type-begin-at')) {
				$index = null;

				foreach ($scrapers as $i => $scraper) {
					if ($scraper->getType() === $beginAt) {
						$index = $i;

						break;
					}
				}

				if ($index === null)
					throw new \InvalidArgumentException('The string "' . $beginAt . '" is not a recognized type');

				$scrapers = array_slice($scrapers, $index);
			} else if ($types = $input->getOption('type')) {
				$scrapers = array_filter($scrapers, function(ScraperInterface $scraper) use ($types): bool {
					return in_array($scraper->getType(), $types);
				});
			}

			if (sizeof($scrapers) === $this->scrapers->count())
				$io->comment('Running scrapers: all');
			else {
				$io->comment(
					'Running scrapers: ' .implode(', ', array_map(function(ScraperInterface $scraper): string {
						return $scraper->getType();
					}, $scrapers))
				);
			}

			$progress = new MultiProgressBar($output);
			$progress->append(sizeof($scrapers));

			$progress->start();

			foreach ($scrapers as $scraper) {
				$context = $contexts[$scraper->getType()] ?? [];

				if ($scraper instanceof ProgressAwareInterface)
					$scraper->setProgressBar($progress);

				$scraper->scrape($context);

				$progress->advance();
			}

			$io->success('Done!');
		}
	}