<?php
	namespace App\Command;

	use App\Console\MultiProgressBar;
	use App\Contrib\DataManagerInterface;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Ailment;
	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\Charm;
	use App\Entity\Decoration;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MotionValue;
	use App\Entity\Skill;
	use App\Entity\Weapon;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ExportCommand extends Command {
		public const ENTITY_LIST = [
			Ailment::class,
			Armor::class,
			ArmorSet::class,
			Charm::class,
			Decoration::class,
			Item::class,
			Location::class,
			Monster::class,
			MotionValue::class,
			Skill::class,
			Weapon::class,
		];

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * @var EntityManagerInterface
		 */
		private $entityManager;

		/**
		 * @var string
		 */
		private $contribDir;

		/**
		 * @var DataManagerInterface[]
		 */
		private $dataManagers = [];

		/**
		 * EntityExportCommand constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ContribManager         $contribManager
		 * @param DataManagerInterface[] $dataManagers
		 * @param string                 $contribDir
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			ContribManager $contribManager,
			array $dataManagers,
			string $contribDir
		) {
			$this->entityManager = $entityManager;
			$this->contribManager = $contribManager;
			$this->contribDir = $contribDir;

			foreach ($dataManagers as $dataManager)
				$this->dataManagers[$dataManager->getEntityClass()] = $dataManager;

			parent::__construct();
		}

		/**
		 * @return void
		 */
		protected function configure(): void {
			$this
				->setName('app:export')
				->addArgument(
					'output-path',
					InputArgument::OPTIONAL,
					'The path the app package should be saved to',
					$this->contribDir
				)
				->addOption(
					'entity',
					null,
					InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
					'If provided, only listed entities will be exported to the package (implies --no-clean)'
				)
				->addOption(
					'target',
					't',
					InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
					'If provided, only export entities matching the given ID (format: "<entity>:<id>"'
				)
				->addOption(
					'no-clean',
					null,
					InputOption::VALUE_NONE,
					'Perform the export without cleaning the package directory first'
				)
				->addOption('yes', 'y', InputOption::VALUE_NONE, 'Answer "yes" to all questions')
				->addOption(
					'dump-only',
					null,
					InputOption::VALUE_NONE,
					'Do not commit or push any changes, only write them to disk'
				);
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$io = new SymfonyStyle($input, $output);

			$path = rtrim($input->getArgument('output-path'), '/');

			if (!$path) {
				$io->error('Cannot export to an empty path (or your filesystem\'s root, ya dingus)');

				return;
			}

			if (!$input->getOption('no-clean')) {
				if (!$input->getOption('yes') && file_exists($path) && sizeof(scandir($path)) > 2) {
					if (!$io->confirm($path . ' is not empty. Are you sure you want to export there?', false)) {
						$io->warning('User cancelled operation.');

						return;
					}
				}

				exec(sprintf('rm -rf %s', escapeshellarg($path . '/json')));
				exec(sprintf('rm -rf %s', escapeshellarg($path . '/assets')));
			}

			if (!file_exists($path))
				mkdir($path, 0755, true);

			if ($input->getOption('dump-only')) {
				$this->contribManager
					->setAllowCommits(false)
					->setAllowPush(false);
			}

			$classes = $input->getOption('entity');

			if (!$classes)
				$classes = self::ENTITY_LIST;
			else {
				$classes = array_map(
					function(string $name): string {
						if (strpos($name, '\\') === false)
							$name = 'App\\Entity\\' . ucfirst($name);

						return $name;
					},
					$classes
				);
			}

			$targetCollections = [];

			if ($rawTargets = $input->getOption('target')) {
				foreach ($rawTargets as $rawTarget) {
					$entity = strtok($rawTarget, ':');
					$id = (int)strtok('');

					if (!$id)
						throw new \InvalidArgumentException('Invalid target descriptor: ' . $rawTarget);

					if (!in_array($entity, $classes))
						$classes[] = $entity;

					if (!isset($targetCollections[$entity]))
						$targetCollections[$entity] = [];

					$targetCollections[$entity][] = $id;
				}
			}

			$progress = new MultiProgressBar($output);
			$progress->append(sizeof($classes));
			$progress->start();

			foreach ($classes as $class) {
				$qb = $this->entityManager->createQueryBuilder()
					->from($class, 'e')
					->select('e');

				if ($targets = ($targetCollections[$class] ?? null)) {
					$qb
						->andWhere('e.id IN (:targets)')
						->setParameter('targets', $targets);
				}

				/** @var EntityInterface[] $entities */
				$entities = $qb->getQuery()->getResult();

				if (!sizeof($entities)) {
					$progress->advance();

					continue;
				}

				$dataManager = $this->dataManagers[$class] ?? null;

				if (!$dataManager)
					throw new \InvalidArgumentException('No manager found for ' . $class);

				$progress->append(sizeof($entities));

				foreach ($entities as $entity) {
					$dataManager->export($entity);

					$progress->advance();
				}

				$progress->advance();
			}

			$progress->finish();

			$io->success('Done!');
		}
	}