<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ArmorSetBonusTransformer extends AbstractTransformer {
		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof ArmorSetBonus))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'ranks')) {
				$entity->getRanks()->clear();

				foreach ($data->ranks as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'pieces',
							'skill',
						]
					);

					if ($missing)
						throw $this->createMissingArrayFieldsException('ranks', $index, $missing);

					$rank = new ArmorSetBonusRank(
						$entity,
						$definition->pieces,
						$this->getSkillRankFromSimpleSkill(
							'ranks[' . $index . ']',
							$definition->skill
						)
					);

					$entity->getRanks()->add($rank);
				}
			}
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		protected function doCreate(object $data): EntityInterface {
			if (!ObjectUtil::isset($data, 'name'))
				throw ValidationException::missingFields(['name']);

			return new ArmorSetBonus($data->name);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof ArmorSetBonus))
				throw $this->createEntityNotSupportedException(get_class($entity));

			$sets = $this->entityManager->getRepository(ArmorSet::class)->findBy(
				[
					'bonus' => $entity,
				]
			);

			foreach ($sets as $set)
				$set->setBonus(null);
		}
	}