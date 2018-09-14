<?php
	namespace App\Import\Importers;

	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Skill;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManagerInterface;

	class AilmentImporter extends AbstractImporter {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * AilmentImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 */
		public function __construct(EntityManagerInterface $entityManager) {
			parent::__construct(Ailment::class);

			$this->entityManager = $entityManager;
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Ailment))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setSlug($data->slug)
				->setDescription($data->description);

			$protection = $entity->getProtection();
			$protection->getItems()->clear();
			$protection->getSkills()->clear();

			foreach ($data->protection->items as $itemId) {
				$item = $this->entityManager->getRepository(Item::class)->find($itemId);

				if (!$item)
					throw $this->createMissingReferenceException('procetion.items', Item::class, $itemId);

				$protection->getItems()->add($item);
			}

			foreach ($data->protection->skills as $skillId) {
				$skill = $this->entityManager->getRepository(Skill::class)->find($skillId);

				if (!$skill)
					throw $this->createMissingReferenceException('protection.skills', Skill::class, $skillId);

				$protection->getSkills()->add($skill);
			}

			$recovery = $entity->getRecovery();
			$recovery->getItems()->clear();

			$recovery->setActions($data->recovery->actions);

			foreach ($data->recovery->items as $itemId) {
				$item = $this->entityManager->getRepository(Item::class)->find($itemId);

				if (!$item)
					throw $this->createMissingReferenceException('recovery.items', Item::class, $itemId);

				$recovery->getItems()->add($item);
			}
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(object $data): EntityInterface {
			$ailment = new Ailment($data->name, $data->description);

			$this->import($ailment, $data);

			return $ailment;
		}
	}