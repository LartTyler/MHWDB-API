<?php
	namespace App\Command;

	use App\Scraping\ScraperCollection;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ScraperListCommand extends Command {
		private $scraperKeys;

		public function __construct(ScraperCollection $scrapers) {
			parent::__construct();

			$this->scraperKeys = array_keys($scrapers->getScrapers());
		}

		protected function configure() {
			$this->setName('app:scrape:list');
		}

		protected function execute(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			$types = [];

			foreach ($this->scraperKeys as $index => $type)
				$types[] = ($index + 1) . ': ' . $type;

			$io->section('Scraper Types (in execution order)');
			$io->listing($types);
		}
	}