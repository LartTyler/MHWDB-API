<?php
	namespace App\Command;

	use App\Console\MultiProgressBar;
	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Import\ImportManager;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManager;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class ImportCommand extends Command {
		/**
		 * @var EntityManagerInterface|EntityManager
		 */
		protected $entityManager;

		/**
		 * @var ImportManager
		 */
		protected $importManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * ImportCommand constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ImportManager          $importManager
		 * @param ContribManager         $contribManager
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			ImportManager $importManager,
			ContribManager $contribManager
		) {
			parent::__construct();

			$this->entityManager = $entityManager;
			$this->importManager = $importManager;
			$this->contribManager = $contribManager;
		}

		/**
		 * @return void
		 */
		protected function configure(): void {
			$this
				->setName('app:import')
				->addOption('entity', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
				->addOption('target', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$io = new SymfonyStyle($input, $output);
			$classes = $input->getOption('entity');

			if (!$classes)
				$classes = ExportCommand::ENTITY_LIST;
			else
				$classes = array_map([$this, 'toFullyQualifiedClassName'], $classes);

			$targetCollections = [];

			if ($rawTargets = $input->getOption('target')) {
				foreach ($rawTargets as $rawTarget) {
					$entity = $this->toFullyQualifiedClassName(strtok($rawTarget, ':'));
					$id = strtok('');

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
				$targets = $targetCollections[$class] ?? [];

				$group = $this->contribManager->getGroup(EntityType::ENTITY_CLASS_MAP[$class]);

				if ($deleted = $group->getJournal()->getDeleted()) {
					/** @var EntityInterface[] $deleted */
					$deleted = $this->entityManager->getRepository($class)->findBy([
						'id' => $deleted,
					]);

					foreach ($deleted as $item) {
						if ($targets && !in_array($item->getId(), $targets))
							continue;

						$this->importManager->delete($item);
						$this->entityManager->remove($item);
					}

					$this->entityManager->flush();

					$group->getJournal()->clearDeleted();
				}

				$allIds = $group->getAllIds();

				if ($created = $group->getJournal()->getCreated())
					$allIds = array_merge(array_keys($created), $allIds);

				if ($targets) {
					$allIds = array_filter($allIds, function($item) use ($targets): bool {
						return in_array((string)$item, $targets);
					});
				}

				$progress->append(sizeof($allIds));

				$replacements = [];

				foreach ($allIds as $id) {
					/** @var EntityInterface|null $entity */
					$entity = $this->entityManager->getRepository($class)->find($id);
					$data = $group->get($id);

					if (!$entity) {
						$entity = $this->importManager->create($class, $data);

						$this->entityManager->persist($entity);

						$replacements[$id] = $entity;
					} else
						$this->importManager->import($entity, $data);

					$progress->advance();
				}

				$this->entityManager->flush();

				foreach ($replacements as $oldId => $entity)
					$group->replace($oldId, $entity->getId());

				$progress->advance();
			}

			$io->success('Done!');
		}

		/**
		 * @param string $class
		 *
		 * @return string
		 */
		protected function toFullyQualifiedClassName(string $class): string {
			if (strpos($class, '\\') === false)
				$class = 'App\\Entity\\' . ucfirst($class);

			return $class;
		}
	}