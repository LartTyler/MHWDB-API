<?php
	namespace App\Command;

	use App\Entity\Armor;
	use App\Scraping\Scrapers\Helpers\KiranicoArmorHelper;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class KiranicoFixArmorNamesCommand extends Command {
		/**
		 * @var ObjectManager
		 */
		private $manager;

		/**
		 * KiranicoFixArmorNamesCommand constructor.
		 *
		 * @param ObjectManager $manager
		 */
		public function __construct(ObjectManager $manager) {
			parent::__construct();

			$this->manager = $manager;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function configure(): void {
			$this->setName('app:fix:kiranico-armor-names');
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			/** @var Armor[] $armors */
			$armors = $this->manager->getRepository('App:Armor')->findAll();
			$progress = new ProgressBar($output, sizeof($armors));

			$progress->start();

			foreach ($armors as $armor) {
				[$name] = KiranicoArmorHelper::parseArmorName($armor->getName());

				$armor->setName($name);

				$progress->advance();
			}

			$this->manager->flush();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}