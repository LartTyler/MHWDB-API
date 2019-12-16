<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Game\Attribute;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ExtractEldersealAttributeCommand extends Command {
		/**
		 * {@inheritdoc}
		 */
		protected static $defaultName = 'app:tools:extract-elderseal-attributes';

		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * ExtractEldersealAttributeCommand constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 */
		public function __construct(EntityManagerInterface $entityManager) {
			parent::__construct();

			$this->entityManager = $entityManager;
		}

		/**
		 * @return void
		 */
		protected function configure(): void {
			$this->addOption(
				'delete-attribute',
				null,
				InputOption::VALUE_NONE,
				'If set, deletes old elderseal attributes during processing'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): int {
			$weapons = $this->entityManager->getRepository(Weapon::class)->findAll();

			$io = new SymfonyStyle($input, $output);
			$io->progressStart(sizeof($weapons));

			foreach ($weapons as $weapon) {
				$io->progressAdvance();

				if ($elderseal = $weapon->getAttribute(Attribute::ELDERSEAL)) {
					$weapon->setElderseal($elderseal);

					if ($input->getOption('delete-attribute'))
						$weapon->removeAttribute(Attribute::ELDERSEAL);
				}
			}

			$this->entityManager->flush();

			$io->progressFinish();
			$io->success('Done!');

			return 0;
		}
	}