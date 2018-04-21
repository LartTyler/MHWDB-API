<?php
	namespace App\Command;

	use App\Entity\Armor;
	use App\Game\Element;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class MigrateResistAttributesCommand extends Command {
		/**
		 * @var ObjectManager
		 */
		private $manager;

		/**
		 * MigrateResistanceDataCommand constructor.
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
			$this->setName('app:migrate:resist-attributes');
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
				$resists = $armor->getResistances();

				foreach (Element::ALL as $element) {
					$value = $armor->getAttribute('resist' . ucfirst($element), 0);

					call_user_func([$resists, 'set' . ucfirst($element)], $value);
				}
				
				$progress->advance();
			}

			$this->manager->flush();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}