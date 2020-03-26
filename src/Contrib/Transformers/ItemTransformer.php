<?php
	namespace App\Contrib\Transformers;

	use App\Entity\AilmentProtection;
	use App\Entity\AilmentRecovery;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Item;
	use App\Entity\Strings\ItemStrings;
	use App\Localization\L10nUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;

	class ItemTransformer extends BaseTransformer {
		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function doCreate(object $data): EntityInterface {
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

			return new Item($data->rarity);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Item))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'name'))
				$this->getStrings($entity)->setName($data->name);

			if (ObjectUtil::isset($data, 'description'))
				$this->getStrings($entity)->setDescription($data->description);

			if (ObjectUtil::isset($data, 'rarity'))
				$entity->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'buyPrice'))
				$entity->setBuyPrice($data->buyPrice);

			if (ObjectUtil::isset($data, 'sellPrice')) {
				$entity->setSellPrice($data->sellPrice);

				// Preserves BC with 1.18
				// TODO Remove in 1.20.0
				$entity->setValue($data->sellPrice);
			}

			if (ObjectUtil::isset($data, 'carryLimit'))
				$entity->setCarryLimit($data->carryLimit);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Item))
				throw EntityTransformerException::subjectNotSupported($entity);

			$costs = $this->entityManager->getRepository(CraftingMaterialCost::class)->findBy(
				[
					'item' => $entity,
				]
			);

			foreach ($costs as $cost)
				$this->entityManager->remove($cost);

			/** @var AilmentProtection[] $protections */
			$protections = $this->entityManager->getRepository(AilmentProtection::class)->findByItem($entity);

			foreach ($protections as $protection)
				$protection->getItems()->removeElement($entity);

			/** @var AilmentRecovery[] $recoveries */
			$recoveries = $this->entityManager->getRepository(AilmentRecovery::class)->findByItem($entity);

			foreach ($recoveries as $recovery)
				$recovery->getItems()->removeElement($entity);
		}

		/**
		 * @param Item $item
		 *
		 * @return ItemStrings
		 */
		protected function getStrings(Item $item): ItemStrings {
			$strings = L10nUtil::findOrCreateStrings($this->getCurrentLocale(), $item);
			assert($strings instanceof ItemStrings);

			return $strings;
		}
	}