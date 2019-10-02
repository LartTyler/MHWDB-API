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
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class ExtractPhialAttributeCommand extends Command {
		public static $defaultName = 'app:tools:extract-phial-attributes';

		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ValidatorInterface
		 */
		protected $validator;

		/**
		 * ExtractPhialAttributeCommand constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface     $validator
		 */
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator) {
			parent::__construct();

			$this->entityManager = $entityManager;
			$this->validator = $validator;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function configure(): void {
			$this->addOption(
				'delete-attribute',
				null,
				InputOption::VALUE_NONE,
				'If set, deletes old phial attributes during processing'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$weapons = $this->entityManager->getRepository(Weapon::class)->findBy(
				[
					'type' => [
						WeaponType::CHARGE_BLADE,
						WeaponType::SWITCH_AXE,
					],
				]
			);

			$io = new SymfonyStyle($input, $output);
			$io->progressStart(sizeof($weapons));

			foreach ($weapons as $weapon) {
				$io->progressAdvance();

				$phialType = $weapon->getAttribute(Attribute::PHIAL_TYPE);

				if ($input->getOption('delete-attribute'))
					$weapon->removeAttribute(Attribute::PHIAL_TYPE);

				if (!$phialType)
					continue;

				if (preg_match('/^([^\d]+) ?(\d+)?$/', $phialType, $matches) !== 1) {
					$io->warning(sprintf('Could not parse value "%s"', $phialType));

					continue;
				}

				$phial = new Phial($weapon, trim($matches[1]));

				if (isset($matches[2]))
					$phial->setDamage((int)$matches[2]);

				$violations = $this->validator->validate($phial);

				if ($violations->count() > 0) {
					$message = sprintf(
						'Phial for Weapon#%d failed validation: %s',
						$weapon->getId(),
						$violations->get(0)->getMessage()
					);

					if ($violations->count() > 1) {
						$message .= sprintf(
							' (and %d other%s)',
							$violations->count() - 1,
							$violations->count() - 1 !== 1 ? 's' : ''
						);
					}

					$io->error($message);

					return;
				}

				$weapon->setPhial($phial);
			}

			$this->entityManager->flush();

			$io->progressFinish();
			$io->success('Done!');
		}
	}