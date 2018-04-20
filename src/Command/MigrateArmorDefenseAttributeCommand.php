<?php
	namespace App\Command;

	use App\Entity\Armor;
	use App\Game\Attribute;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class MigrateArmorDefenseAttributeCommand extends Command {
		/**
		 * @var ObjectManager
		 */
		private $manager;

		/**
		 * MigrateArmorDefenseAttributeCommand constructor.
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
			$this->setName('app:migrate:armor-defense-attribute');
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
				$defense = $armor->getAttribute(Attribute::DEFENSE, 0);
				$values = $armor->getDefense();

				$values->setBase($defense);

				if (!$values->getMax())
					$values->setMax($defense);

				if (!$values->getAugmented())
					$values->setAugmented($defense);

				$progress->advance();
			}

			$this->manager->flush();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}