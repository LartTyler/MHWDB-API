<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\IntegrityException;
	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ArmorSetTransformer extends AbstractTransformer {
		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof ArmorSet))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'rank'))
				$entity->setRank($data->rank);

			if (ObjectUtil::isset($data, 'pieces')) {
				foreach ($entity->getPieces() as $piece)
					$piece->setArmorSet(null);

				$entity->getPieces()->clear();

				foreach ($data->pieces as $index => $armorId) {
					$armor = $this->entityManager->getRepository(Armor::class)->find($armorId);

					if (!$armor)
						throw IntegrityException::missingReference('pieces[' . $index . ']', 'Armor');

					$entity->getPieces()->add($armor);

					$armor->setArmorSet($entity);
				}
			}

			if (ObjectUtil::isset($data, 'bonus')) {
				$bonus = $this->entityManager->getRepository(ArmorSetBonus::class)->find($data->bonus);

				if (!$bonus)
					throw IntegrityException::missingReference('bonus', 'ArmorSetBonus');

				$entity->setBonus($bonus);
			}
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
					'rank',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new ArmorSet($data->name, $data->rank);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof ArmorSet))
				throw $this->createEntityNotSupportedException(get_class($entity));

			foreach ($entity->getPieces() as $piece)
				$piece->setArmorSet(null);
		}
	}