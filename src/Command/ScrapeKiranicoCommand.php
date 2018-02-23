<?php
	namespace App\Command;

	use App\Scraper\Kiranico\KiranicoScrapeTarget;
	use App\Scraper\Kiranico\Scrapers\KiranicoSkillsScraper;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;

	class ScrapeKiranicoCommand extends Command {
		protected function configure() {
			$this
				->setName('scrape:kiranico');
		}

		protected function execute(InputInterface $input, OutputInterface $output) {
			$target = new KiranicoScrapeTarget();
			$target->addScraper(new KiranicoSkillsScraper($target));

			foreach ($target->scrape() as $item)
				var_dump($item);
		}
	}