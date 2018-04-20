<?php
	namespace App\Command;

	use App\Entity\Armor;
	use App\Entity\Slot;
	use App\Game\Attribute;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class MigrateSlotDataCommand extends Command {
		private const SLOT_KEYS = [
			Attribute::SLOT_RANK_1,
			Attribute::SLOT_RANK_2,
			Attribute::SLOT_RANK_3,
		];

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * MigrateSlotDataCommand constructor.
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
			$this->setName('app:migrate:slot-attributes');
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			/** @var Armor[] $armors */
			$armors = $this->manager->getRepository('App:Armor')->findAll();
			$progress = new ProgressBar($output, sizeof($armors));

			foreach ($armors as $armor) {
				$armor->getSlots()->clear();

				foreach (self::SLOT_KEYS as $slotKey) {
					$count = $armor->getAttribute($slotKey);

					if (!$count)
						continue;

					$rank = (int)substr($slotKey, -1);

					for ($i = 0; $i < $count; $i++)
						$armor->getSlots()->add(new Slot($rank));
				}

				$progress->advance();
			}

			$this->manager->flush();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}