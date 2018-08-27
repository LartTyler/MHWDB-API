<?php
	namespace App\Command;

	use App\Scraping\ScraperCollection;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ScraperListCommand extends Command {
		/**
		 * @var array
		 */
		private $scraperKeys;

		/**
		 * ScraperListCommand constructor.
		 *
		 * @param ScraperCollection $scrapers
		 */
		public function __construct(ScraperCollection $scrapers) {
			parent::__construct();

			$this->scraperKeys = array_keys($scrapers->getScrapers());
		}

		/**
		 * @return void
		 */
		protected function configure() {
			$this->setName('app:scrapers:list');
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			$types = [];

			foreach ($this->scraperKeys as $index => $type)
				$types[] = ($index + 1) . ': ' . $type;

			$io->section('Scraper Types (in execution order)');
			$io->listing($types);
		}
	}