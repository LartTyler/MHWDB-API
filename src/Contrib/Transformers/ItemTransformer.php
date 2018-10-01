<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\AilmentProtection;
	use App\Entity\AilmentRecovery;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Item;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ItemTransformer extends AbstractTransformer {
		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Item))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'description'))
				$entity->setDescription($data->description);

			if (ObjectUtil::isset($data, 'rarity'))
				$entity->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'value'))
				$entity->setValue($data->value);

			if (ObjectUtil::isset($data, 'carryLimit'))
				$entity->setCarryLimit($data->carryLimit);
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		protected function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'name',
					'description',
					'rarity',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Item($data->name, $data->description, $data->rarity);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Item))
				throw $this->createEntityNotSupportedException(get_class($entity));

			$costs = $this->entityManager->getRepository(CraftingMaterialCost::class)->findBy(
				[
					'item' => $entity,
				]
			);

			foreach ($costs as $cost)
				$this->entityManager->remove($cost);

			$protections = $this->entityManager->getRepository(AilmentProtection::class)->findByItem($entity);

			foreach ($protections as $protection)
				$protection->getItems()->removeElement($entity);

			$recoveries = $this->entityManager->getRepository(AilmentRecovery::class)->findByItem($entity);

			foreach ($recoveries as $recovery)
				$recovery->getItems()->removeElement($entity);
		}
	}