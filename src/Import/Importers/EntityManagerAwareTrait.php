<?php
	namespace App\Import\Importers;

	use Doctrine\ORM\EntityManagerInterface;

	trait EntityManagerAwareTrait {
		/**
		 * @var EntityManagerInterface|null
		 */
		protected $entityManager = null;

		/**
		 * @required
		 *
		 * @param EntityManagerInterface $entityManager
		 *
		 * @return void
		 */
		public function setEntityManager(EntityManagerInterface $entityManager) {
			$this->entityManager = $entityManager;
		}
	}