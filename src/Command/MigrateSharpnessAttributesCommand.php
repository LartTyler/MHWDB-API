<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Game\Sharpness;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class MigrateSharpnessAttributesCommand extends Command {
		/**
		 * @var ObjectManager
		 */
		private $manager;

		/**
		 * MigrateSharpnessAttributesCommand constructor.
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
			$this->setName('app:migrate:sharpness');
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			/** @var Weapon[] $weapons */
			$weapons = $this->manager->getRepository('App:Weapon')->findAll();
			$progress = new ProgressBar($output, sizeof($weapons));

			$progress->start();

			foreach ($weapons as $weapon) {
				$sharpness = $weapon->getSharpness();

				foreach (Sharpness::ALL as $key) {
					$value = $weapon->getAttribute($key);

					if ($value === null)
						continue;

					$method = 'set' . ucfirst(substr($key, 9));

					if (!method_exists($sharpness, $method))
						throw new \RuntimeException('Could not find method to set ' . $key);

					call_user_func([$sharpness, $method], $value);
				}

				$progress->advance();
			}

			$this->manager->flush();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}