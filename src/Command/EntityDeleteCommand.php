<?php
	namespace App\Command;

	use App\Entity\Armor;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class EntityDeleteCommand extends Command {
		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * EntityDeleteCommand constructor.
		 *
		 * @param ObjectManager $manager
		 */
		public function __construct(ObjectManager $manager) {
			parent::__construct();

			$this->manager = $manager;
		}

		protected function configure() {
			$this
				->setName('app:entity:delete')
				->addArgument('entity', InputArgument::REQUIRED)
				->addArgument('id', InputArgument::REQUIRED);
		}

		protected function execute(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);

			/** @var EntityInterface|null $entity */
			$entity = $this->manager->getRepository('App:' . ucfirst($input->getArgument('entity')))
				->find((int)$input->getArgument('id'));

			if (!$entity) {
				if (!$output->isQuiet())
					$io->error('No ' . $input->getArgument('entity') . ' exists with ID ' . $input->getArgument('id'));

				return 1;
			}

			$confirm = sprintf('Are you sure you want to delete %s#%d?', get_class($entity), $entity->getId());

			if ($input->isInteractive() && !$io->confirm($confirm, false))
				return 0;

			if ($entity instanceof Armor)
				$entity->getCrafting()->getMaterials()->clear();

			$this->manager->remove($entity);
			$this->manager->flush();

			$io->success('Done!');

			return 0;
		}
	}