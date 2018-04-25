<?php
	namespace App\Command;

	use App\Console\MultiProgressBar;
	use App\Entity\Weapon;
	use App\Game\Attribute;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class MigrateElementAttributesCommand extends Command {
		/**
		 * @var ObjectManager
		 */
		private $manager;

		/**
		 * MigrateElementAttributesCommand constructor.
		 *
		 * @param ObjectManager $manager
		 */
		public function __construct(ObjectManager $manager) {
			parent::__construct();

			$this->manager = $manager;
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$progress = new MultiProgressBar($output);
			$progress->append(2);

			$progress->start();

			$elements = $this->manager->getRepository('App:WeaponElement')->findAll();
			$progress->append(sizeof($elements));

			foreach ($elements as $element) {
				$this->manager->remove($element);

				$progress->advance();
			}

			$this->manager->flush();

			$progress->advance();

			/** @var Weapon[] $weapons */
			$weapons = $this->manager->getRepository('App:Weapon')->findAll();
			$progress->append(sizeof($weapons));

			foreach ($weapons as $weapon) {
				$weapon->getElements()->clear();

				if ($element = $weapon->getAttribute(Attribute::ELEM_TYPE))
					$weapon->setElement(strtolower($element), $weapon->getAttribute(Attribute::ELEM_DAMAGE),
						$weapon->getAttribute(Attribute::ELEM_HIDDEN, false));

				if ($element = $weapon->getAttribute(Attribute::ELEM_TYPE_2))
					$weapon->setElement(strtolower($element), $weapon->getAttribute(Attribute::ELEM_DAMAGE_2,
						$weapon->getAttribute(Attribute::ELEM_HIDDEN_2, false)));

				$progress->advance();
			}

			$this->manager->flush();

			$progress->advance();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}