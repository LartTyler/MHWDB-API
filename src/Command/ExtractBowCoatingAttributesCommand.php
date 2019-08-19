<?php
	namespace App\Command;

	use App\Entity\Phial;
	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ExtractBowCoatingAttributesCommand extends Command {
		protected static $defaultName = 'app:tools:extract-bow-coating-attributes';

		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * ExtractBowCoatingAttributesCommand constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 */
		public function __construct(EntityManagerInterface $entityManager) {
			parent::__construct();

			$this->entityManager = $entityManager;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function configure(): void {
			$this->addOption(
				'delete-attribute',
				null,
				InputOption::VALUE_NONE,
				'If set, deletes old bow coating attributes during processing'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$weapons = $this->entityManager->getRepository(Weapon::class)->findBy(
				[
					'type' => WeaponType::BOW,
				]
			);

			$io = new SymfonyStyle($input, $output);
			$io->progressStart(sizeof($weapons));

			foreach ($weapons as $weapon) {
				$io->progressAdvance();

				if ($coatings = $weapon->getAttribute(Attribute::COATINGS)) {
					$weapon->setCoatings($coatings);

					if ($input->getOption('delete-attribute'))
						$weapon->removeAttribute(Attribute::COATINGS);
				}
			}

			$this->entityManager->flush();

			$io->progressFinish();
			$io->success('Done!');
		}
	}