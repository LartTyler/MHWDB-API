<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\QueryBuilder;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Symfony\Component\Validator\ConstraintViolationListInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	abstract class AbstractExtractWeaponAttributeCommand extends Command {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ValidatorInterface
		 */
		protected $validator;

		/**
		 * AbstractExtractWeaponAttributeCommand constructor.
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
		protected function configure() {
			$this
				->addOption(
					'delete-attribute',
					null,
					InputOption::VALUE_NONE,
					'If set, the attribute will be deleted during processing'
				)
				->addOption(
					'skip-validation',
					null,
					InputOption::VALUE_NONE,
					'Skip validating entities after attributes are extracted'
				);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): int {
			$qb = $this->entityManager->createQueryBuilder()
				->from(Weapon::class, 'w')
				->select('w');

			$this->addQueryBuilderClauses($qb);

			/** @var Weapon[] $weapons */
			$weapons = $qb->getQuery()->getResult();

			$io = new SymfonyStyle($input, $output);
			$io->progressStart(sizeof($weapons));

			foreach ($weapons as $weapon) {
				$io->progressAdvance();

				$this->process($weapon, $input->getOption('delete-attribute'));

				if ($input->getOption('skip-validation'))
					continue;

				$violations = $this->validate($weapon);

				if ($violations->count() > 0) {
					$message = sprintf(
						'Weapon#%d failed validation after update: [%s] %s',
						$weapon->getId(),
						$violations->get(0)->getPropertyPath(),
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

					return 1;
				}
			}

			$this->entityManager->flush();

			$io->progressFinish();
			$io->success('Done!');

			return 0;
		}

		/**
		 * @param QueryBuilder $queryBuilder
		 *
		 * @return void
		 */
		protected function addQueryBuilderClauses(QueryBuilder $queryBuilder): void {
		}

		/**
		 * @param Weapon $weapon
		 *
		 * @param bool   $deleteAttribute
		 *
		 * @return void
		 */
		protected abstract function process(Weapon $weapon, bool $deleteAttribute): void;

		/**
		 * @param Weapon $weapon
		 *
		 * @return ConstraintViolationListInterface|null
		 */
		protected function validate(Weapon $weapon): ?ConstraintViolationListInterface {
			return $this->validator->validate($weapon);
		}
	}