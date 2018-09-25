<?php
	namespace App\Import\Importers;

	use App\Contrib\Delete\DependentObjectsFailureReason;
	use App\Contrib\EntityType;
	use App\Contrib\Exceptions\DeleteFailedException;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Monster;
	use App\Entity\Skill;
	use App\Import\ManagedDeleteInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManagerInterface;

	class AilmentImporter extends AbstractImporter implements ManagedDeleteInterface {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * AilmentImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ContribManager         $contribManager
		 */
		public function __construct(EntityManagerInterface $entityManager, ContribManager $contribManager) {
			parent::__construct(Ailment::class);

			$this->entityManager = $entityManager;
			$this->contribManager = $contribManager;
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

			$itemGroup = $this->contribManager->getGroup(EntityType::ITEMS);

			foreach ($data->protection->items as $itemId) {
				$item = $this->entityManager->getRepository(Item::class)->find($itemGroup->getTrueId($itemId));

				if (!$item)
					throw $this->createMissingReferenceException('protection.items', Item::class, $itemId);

				$protection->getItems()->add($item);
			}

			$skillGroup = $this->contribManager->getGroup(EntityType::SKILLS);

			foreach ($data->protection->skills as $skillId) {
				$skill = $this->entityManager->getRepository(Skill::class)->find($skillGroup->getTrueId($skillId));

				if (!$skill)
					throw $this->createMissingReferenceException('protection.skills', Skill::class, $skillId);

				$protection->getSkills()->add($skill);
			}

			$recovery = $entity->getRecovery();
			$recovery->getItems()->clear();

			$recovery->setActions($data->recovery->actions);

			foreach ($data->recovery->items as $itemId) {
				$item = $this->entityManager->getRepository(Item::class)->find($itemGroup->getTrueId($itemId));

				if (!$item)
					throw $this->createMissingReferenceException('recovery.items', Item::class, $itemId);

				$recovery->getItems()->add($item);
			}
		}

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$ailment = new Ailment($data->name, $data->description);
			$ailment->setId($id);

			$this->import($ailment, $data);

			return $ailment;
		}

		/**
		 * @param EntityInterface|Ailment $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void {
			$qb = $this->entityManager->createQueryBuilder()
				->from(Monster::class, 'm')
				->leftJoin('m.ailments', 'a')
				->where('a.id = :ailment')
				->setParameter('ailment', $entity);

			$count = (int)$qb
				->select('COUNT(m)')
				->getQuery()
					->getSingleScalarResult();

			if ($count > 0) {
				$sample = $qb
					->select('m.id AS id')
					->setMaxResults(5)
					->getQuery()
						->getArrayResult();

				$sample = array_map(function(array $row): int {
					return $row['id'];
				}, $sample);

				throw new DeleteFailedException(new DependentObjectsFailureReason('Monster', $sample, $count));
			}
		}
	}