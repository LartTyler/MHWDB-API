<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Entity\WeaponSharpness;
	use App\Scraping\Scrapers\Helpers\MHWGHelper;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class MHWGSyncSharpnessValues extends Command {
		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * MHWGSyncSharpnessValues constructor.
		 *
		 * @param ObjectManager $manager
		 */
		public function __construct(ObjectManager $manager) {
			parent::__construct();

			$this->manager = $manager;
		}

		/**
		 * @return void
		 */
		protected function configure(): void {
			$this->setName('app:fix:sync-mhwg-sharpness-values');
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return int|null|void
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			/** @var Weapon[] $weapons */
			$weapons = array_filter($this->manager
				->getRepository('App:Weapon')->findAll(), function(Weapon $weapon): bool {
					return $weapon->getDurability()->count() > 0;
				});

			$progress = new ProgressBar($output, sizeof($weapons));
			$progress->start();

			foreach ($weapons as $weapon) {
				if (!$weapon->getDurability()->count())
					continue;

				/** @var WeaponSharpness $baseSharpness */
				$baseSharpness = $weapon->getDurability()->first();

				$weapon->getSharpness()
					->setRed(MHWGHelper::toOldSharpnessValue($baseSharpness->getRed()))
					->setOrange(MHWGHelper::toOldSharpnessValue($baseSharpness->getOrange()))
					->setYellow(MHWGHelper::toOldSharpnessValue($baseSharpness->getYellow()))
					->setGreen(MHWGHelper::toOldSharpnessValue($baseSharpness->getGreen()))
					->setBlue(MHWGHelper::toOldSharpnessValue($baseSharpness->getBlue()))
					->setWhite(MHWGHelper::toOldSharpnessValue($baseSharpness->getWhite()));

				$progress->advance();
			}

			$this->manager->flush();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}