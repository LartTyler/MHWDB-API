<?php
	namespace App\Command;

	use App\Console\MultiProgressBar;
	use App\Entity\Armor;
	use App\Entity\Slot;
	use App\Entity\Weapon;
	use App\Game\Attribute;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class MigrateSlotAttributesCommand extends Command {
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
			$progress = new MultiProgressBar($output);
			$progress->append(2);

			$progress->start();

			/** @var Armor[] $armors */
			$armors = $this->manager->getRepository('App:Armor')->findAll();

			$progress->append(sizeof($armors));

			foreach ($armors as $armor) {
				$this->migrateSlots($armor);

				$progress->advance();
			}

			$this->manager->flush();

			$progress->advance();

			/** @var Weapon[] $weapons */
			$weapons = $this->manager->getRepository('App:Weapon')->findAll();

			$progress->append(sizeof($weapons));

			foreach ($weapons as $weapon) {
				$this->migrateSlots($weapon);

				$progress->advance();
			}

			$this->manager->flush();

			$progress->advance();

			(new SymfonyStyle($input, $output))->success('Done!');
		}

		/**
		 * @param Armor|Weapon $entity
		 *
		 * @return void
		 */
		private function migrateSlots($entity): void {
			$entity->getSlots()->clear();

			foreach (self::SLOT_KEYS as $slotKey) {
				$count = $entity->getAttribute($slotKey);

				if (!$count)
					continue;

				$rank = (int)substr($slotKey, -1);

				for ($i = 0; $i < $count; $i++)
					$entity->getSlots()->add(new Slot($rank));
			}
		}
	}