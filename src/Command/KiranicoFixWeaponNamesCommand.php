<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Scraping\Scrapers\Helpers\KiranicoHelper;
	use Doctrine\Common\Persistence\ObjectManager;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Helper\ProgressBar;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class KiranicoFixWeaponNamesCommand extends Command {
		/**
		 * @var ObjectManager|EntityManagerInterface
		 */
		private $manager;

		/**
		 * KiranicoFixWeaponNamesCommand constructor.
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
		protected function configure() {
			$this
				->setName('app:kiranico:fix-weapon-names')
				->addOption('type', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED);
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$qb = $this->manager->createQueryBuilder()
				->from('App:Weapon', 'w')
				->select('w');

			if ($types = $input->getOption('type'))
				$qb
					->andWhere('w.type IN (:types)')
					->setParameter('types', $types);

			/** @var Weapon[] $weapons */
			$weapons = $qb->getQuery()->getResult();

			$progress = new ProgressBar($output, sizeof($weapons));

			foreach ($weapons as $weapon) {
				$weapon->setName(KiranicoHelper::fixWeaponName($weapon->getName(), $weapon->getType()));

				$progress->advance();
			}

			$this->manager->flush();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}