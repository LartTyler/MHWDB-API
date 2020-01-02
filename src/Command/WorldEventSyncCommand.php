<?php
	namespace App\Command;

	use App\Entity\WorldEvent;
	use App\Game\Expansion;
	use App\WorldEvent\WorldEventReader;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class WorldEventSyncCommand extends Command {
		/**
		 * @var WorldEventReader
		 */
		protected $eventReader;

		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * WorldEventScrapeCommand constructor.
		 *
		 * @param WorldEventReader       $eventReader
		 * @param EntityManagerInterface $entityManager
		 */
		public function __construct(WorldEventReader $eventReader, EntityManagerInterface $entityManager) {
			parent::__construct();

			$this->eventReader = $eventReader;
			$this->entityManager = $entityManager;
		}

		/**
		 * @return void
		 */
		protected function configure(): void {
			$this
				->addArgument('platform', InputArgument::REQUIRED)
				->addArgument('expansion', InputArgument::OPTIONAL, '', Expansion::BASE)
				->addOption('sleep', null, InputOption::VALUE_REQUIRED, '', 5);
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return int
		 */
		protected function execute(InputInterface $input, OutputInterface $output): int {
			$events = $this->eventReader->read(
				$input->getArgument('platform'),
				$input->getArgument('expansion'),
				(int)$input->getOption('sleep')
			);

			$added = 0;

			foreach ($events as $event) {
				$this->entityManager->persist($event);

				++$added;
			}

			$expired = $this->entityManager->createQueryBuilder()
				->from(WorldEvent::class, 'e')
				->select('e')
				->where('e.endTimestamp <= :now')
				->setParameter('now', new \DateTime('now', new \DateTimeZone('UTC')))
				->getQuery()
				->getResult();

			foreach ($expired as $item)
				$this->entityManager->remove($item);

			$this->entityManager->flush();

			(new SymfonyStyle($input, $output))->success(
				sprintf(
					'Added %d new event%s and deleted %d expired event%s.',
					$added,
					$added !== 1 ? 's' : '',
					sizeof($expired),
					sizeof($expired) !== 1 ? 's' : ''
				)
			);

			return 0;
		}
	}