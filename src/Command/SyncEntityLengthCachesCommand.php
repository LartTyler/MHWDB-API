<?php
	namespace App\Command;

	use App\Console\MultiProgressBar;
	use App\Entity\Ammo;
	use App\Entity\Armor;
	use App\Entity\ArmorCraftingInfo;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\CharmRankCraftingInfo;
	use App\Entity\Decoration;
	use App\Entity\LengthCachingEntityInterface;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MotionValue;
	use App\Entity\Skill;
	use App\Entity\Weapon;
	use App\Entity\WeaponCraftingInfo;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;

	class SyncEntityLengthCachesCommand extends Command {
		/**
		 * @var string
		 */
		public static $defaultName = 'app:tools:sync-length-caches';

		protected const CLASSES = [
			Ammo::class,
			Armor::class,
			ArmorCraftingInfo::class,
			ArmorSet::class,
			ArmorSetBonus::class,
			Charm::class,
			CharmRank::class,
			CharmRankCraftingInfo::class,
			Decoration::class,
			Location::class,
			Monster::class,
			MotionValue::class,
			Skill::class,
			Weapon::class,
			WeaponCraftingInfo::class,
		];

		/**
		 * @var ObjectManager
		 */
		protected $manager;

		/**
		 * SyncEntityLengthCachesCommand constructor.
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
			$this->addOption('entity', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$classes = array_map(function(string $class): string {
				return strpos($class, '\\') !== false ? $class : 'App\\Entity\\' . $class;
			}, $input->getOption('entity'));

			$progress = new MultiProgressBar($output);
			$progress->append($classes ? sizeof($classes) : sizeof(self::CLASSES));

			foreach (self::CLASSES as $class) {
				if ($classes && !in_array($class, $classes))
					continue;
				else if (!is_a($class, LengthCachingEntityInterface::class, true))
					throw new \Exception($class . ' does not implement ' . LengthCachingEntityInterface::class);

				/** @var LengthCachingEntityInterface[] $entities */
				$entities = $this->manager->getRepository($class)->findAll();

				foreach ($entities as $entity)
					$entity->syncLengthFields();

				$this->manager->flush();

				$progress->advance();
			}
		}
	}