<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\IntegrityException;
	use App\Contrib\Exceptions\ValidationException;
	use App\Contrib\TransformerInterface;
	use App\Entity\ArmorAssets;
	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Item;
	use App\Entity\Skill;
	use App\Entity\WeaponAssets;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\ORM\EntityManagerInterface;

	abstract class AbstractTransformer implements TransformerInterface {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var string
		 */
		protected $entityClass;

		/**
		 * AbstractTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param string                 $entityClass
		 */
		public function __construct(EntityManagerInterface $entityManager, string $entityClass) {
			$this->entityManager = $entityManager;
			$this->entityClass = $entityClass;
		}

		/**
		 * @return string
		 */
		public function getEntityClass(): string {
			return $this->entityClass;
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(object $data): EntityInterface {
			$entity = $this->doCreate($data);
			$this->update($entity, $data);

			$this->entityManager->persist($entity);

			return $entity;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void {
			$this->doDelete($entity);

			$this->entityManager->remove($entity);
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		protected abstract function doCreate(object $data): EntityInterface;

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected abstract function doDelete(EntityInterface $entity): void;

		/**
		 * @return \InvalidArgumentException
		 */
		protected function createEntityNotSupportedException(): \InvalidArgumentException {
			return new \InvalidArgumentException(
				'This transformer only supports ' . $this->getEntityClass() . ' entities'
			);
		}

		/**
		 * @param string     $path
		 * @param Collection $collection
		 * @param string     $class
		 * @param int[]      $ids
		 *
		 * @return void
		 */
		protected function populateFromIdArray(string $path, Collection $collection, string $class, array $ids): void {
			$collection->clear();

			foreach ($ids as $index => $id) {
				$value = $this->entityManager->getRepository($class)->find($id);

				if (!$value) {
					$name = substr($class, strrpos($class, '\\') + 1);

					throw IntegrityException::missingReference($path . '[' . $index . ']', $name);
				}

				$collection->add($value);
			}
		}

		/**
		 * @param string     $path
		 * @param Collection $collection
		 * @param object[]   $ranks
		 *
		 * @return void
		 */
		protected function populateFromSimpleSkillsArray(string $path, Collection $collection, array $ranks): void {
			$collection->clear();

			foreach ($ranks as $index => $rank) {
				$missing = ObjectUtil::getMissingProperties($rank, [
					'skill',
					'level',
				]);

				if ($missing)
					throw ValidationException::missingFields($missing);

				$skill = $this->entityManager->getRepository(Skill::class)->find($rank->skill);

				if (!$skill)
					throw IntegrityException::missingReference($path . '[' . $index . '].skill', 'Skill');

				$skillRank = $skill->getRank($rank->level);

				if (!$skillRank)
					throw IntegrityException::missingReference($path . '[' . $index . '].level', 'SkillRank');

				$collection->add($skillRank);
			}
		}

		/**
		 * @param string     $path
		 * @param Collection $collection
		 * @param object[]   $costs
		 *
		 * @return void
		 */
		protected function populateFromSimpleCostArray(string $path, Collection $collection, array $costs): void {
			$collection->clear();

			foreach ($costs as $index => $cost) {
				$missing = ObjectUtil::getMissingProperties($cost, [
					'item',
					'quantity',
				]);

				if ($missing)
					throw ValidationException::missingFields($missing);

				$item = $this->entityManager->getRepository(Item::class)->find($cost->item);

				if (!$item)
					throw IntegrityException::missingReference($path . '[' . $index . '].item', 'Item');

				$collection->add(new CraftingMaterialCost($item, $cost->quantity));
			}
		}

		/**
		 * @param Asset $asset
		 *
		 * @return int
		 */
		protected function getAssetUsageCount(Asset $asset): int {
			$count = (int)$this->entityManager->createQueryBuilder()
				->from(WeaponAssets::class, 'a')
				->select('COUNT(a)')
				->where('a.icon = :asset')
				->orWhere('a.image = :asset')
				->setParameter('asset', $asset)
				->getQuery()
				->getSingleScalarResult();

			return $count + (int)$this->entityManager->createQueryBuilder()
					->from(ArmorAssets::class, 'a')
					->select('COUNT(a)')
					->where('a.imageFemale = :asset')
					->orWhere('a.imageMale = :asset')
					->setParameter('asset', $asset)
					->getQuery()
					->getSingleScalarResult();
		}
	}