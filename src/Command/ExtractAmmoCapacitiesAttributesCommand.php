<?php
	namespace App\Command;

	use App\Entity\Ammo;
	use App\Entity\Weapon;
	use App\Game\Attribute;
	use App\Game\WeaponType;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ExtractAmmoCapacitiesAttributesCommand extends Command {
		public static $defaultName = 'app:tools:extract-ammo-capacities-attributes';

		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * ExtractAmmoCapacitiesAttributesCommand constructor.
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
				'If set, deletes old ammo capacities attributes during processing'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$weapons = $this->entityManager->getRepository(Weapon::class)->findBy(
				[
					'type' => [
						WeaponType::LIGHT_BOWGUN,
						WeaponType::HEAVY_BOWGUN,
					],
				]
			);

			$io = new SymfonyStyle($input, $output);
			$io->progressStart(sizeof($weapons));

			foreach ($weapons as $weapon) {
				$io->progressAdvance();

				if ($capacities = $weapon->getAttribute(Attribute::AMMO_CAPACITIES)) {
					$weapon->getAmmo()->clear();

					foreach ($capacities as $type => $capacity) {
						$ammo = new Ammo($weapon, $type);
						$ammo->setCapacities($capacity);

						if (!$ammo->isEmpty())
							$weapon->getAmmo()->add($ammo);
					}
				}
			}

			$this->entityManager->flush();

			$io->progressFinish();
			$io->success('Done!');
		}
	}