<?php
	namespace App\Command;

	use App\Entity\Weapon;
	use App\Game\Attribute;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class FixEmptyAffinityValuesCommand extends Command {
		/**
		 * @var EntityManagerInterface
		 */
		private $manager;

		/**
		 * FixEmptyAffinityValuesCommand constructor.
		 *
		 * @param EntityManagerInterface $manager
		 */
		public function __construct(EntityManagerInterface $manager) {
			parent::__construct();

			$this->manager = $manager;
		}

		/**
		 * @return void
		 */
		protected function configure() {
			$this->setName('app:fix:empty-affinity-values');
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			/** @var Weapon[] $weapons */
			$weapons = $this->manager->createQueryBuilder()
				->from(Weapon::class, 'w')
				->select('w')
				->where('JSON_UNQUOTE(JSON_EXTRACT(w.attributes, :path)) = 0')
				->setParameter('path', '$.affinity')
				->getQuery()
					->getResult();

			foreach ($weapons as $weapon) {
				if ($weapon->getAttribute(Attribute::AFFINITY) !== 0)
					continue;

				$weapon->removeAttribute(Attribute::AFFINITY);
			}

			$this->manager->flush();

			(new SymfonyStyle($input, $output))->success('Done!');
		}
	}