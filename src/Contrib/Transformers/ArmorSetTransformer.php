<?php
	namespace App\Contrib\Transformers;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;

	class ArmorSetTransformer extends BaseTransformer {
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
		public function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof ArmorSet))
				throw EntityTransformerException::subjectNotSupported($entity);

			foreach ($entity->getPieces() as $piece)
				$piece->setArmorSet(null);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof ArmorSet))
				throw EntityTransformerException::subjectNotSupported($entity);

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
				/** @var ArmorSetBonus|null $bonus */
				$bonus = $this->entityManager->getRepository(ArmorSetBonus::class)->find($data->bonus);

				if (!$bonus)
					throw IntegrityException::missingReference('bonus', 'ArmorSetBonus');

				$entity->setBonus($bonus);
			}
		}
	}