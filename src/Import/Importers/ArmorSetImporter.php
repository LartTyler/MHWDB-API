<?php
	namespace App\Import\Importers;

	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribManager;
	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Import\ManagedDeleteInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManagerInterface;

	class ArmorSetImporter extends AbstractImporter implements ManagedDeleteInterface {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var ContribManager
		 */
		protected $contribManager;

		/**
		 * ArmorSetImporter constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ContribManager         $contribManager
		 */
		public function __construct(EntityManagerInterface $entityManager, ContribManager $contribManager) {
			parent::__construct(ArmorSet::class);

			$this->entityManager = $entityManager;
			$this->contribManager = $contribManager;
		}

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$armorSet = new ArmorSet($data->name, $data->rank);
			$armorSet->setId($id);

			$this->import($armorSet, $data);

			return $armorSet;
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof ArmorSet))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setRank($data->rank);

			$entity->getPieces()->clear();

			$armorGroup = $this->contribManager->getGroup(EntityType::ARMOR);

			foreach ($data->pieces as $i => $armorId) {
				$armorId = $armorGroup->getTrueId($armorId);
				$armor = $this->entityManager->getRepository(Armor::class)->find($armorId);

				if (!$armor)
					throw $this->createMissingReferenceException('pieces[' . $i . ']', Armor::class, $armorId);

				$entity->getPieces()->add($armor);
				$armor->setArmorSet($entity);
			}

			if ($bonusId = $data->bonus) {
				$bonus = $this->entityManager->getRepository(ArmorSetBonus::class)->find($bonusId);

				if (!$bonus)
					throw $this->createMissingReferenceException('bonus', ArmorSetBonus::class, $bonusId);

				$entity->setBonus($bonus);
			} else
				$entity->setBonus(null);
		}

		/**
		 * @param EntityInterface|ArmorSet $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void {
			foreach ($entity->getPieces() as $piece)
				$piece->setArmorSet(null);

			$otherSets = (int)$this->entityManager->createQueryBuilder()
				->from(ArmorSet::class, 's')
				->select('COUNT(s)')
				->where('s.id != :set')
				->andWhere('IDENTITY(s.bonus) = :bonus')
				->setParameter('set', $entity)
				->setParameter('bonus', $entity->getBonus())
				->getQuery()
					->getSingleScalarResult();

			if ($otherSets === 0)
				$this->entityManager->remove($entity->getBonus());
		}
	}