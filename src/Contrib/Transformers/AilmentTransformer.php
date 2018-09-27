<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Monster;
	use App\Entity\Skill;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class AilmentTransformer extends AbstractTransformer {
		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Ailment))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'description'))
				$entity->setDescription($data->description);

			if (ObjectUtil::isset($data, 'recovery')) {
				$recovery = $entity->getRecovery();
				$definition = $data->recovery;

				if (ObjectUtil::isset($definition, 'items')) {
					$this->populateFromIdArray(
						'recovery.items',
						$recovery->getItems(),
						Item::class,
						$definition->items
					);
				}

				if (ObjectUtil::isset($definition, 'actions'))
					$recovery->setActions($definition->actions);
			}

			if (ObjectUtil::isset($data, 'protection')) {
				$protection = $entity->getProtection();
				$definition = $data->protection;

				if (ObjectUtil::isset($definition, 'items')) {
					$this->populateFromIdArray(
						'protection.items',
						$protection->getItems(),
						Item::class,
						$definition->items
					);
				}

				if (ObjectUtil::isset($definition, 'skills')) {
					$this->populateFromIdArray(
						'protection.skills',
						$protection->getSkills(),
						Skill::class,
						$definition->skills
					);
				}
			}
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		protected function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties($data, [
				'name',
				'description',
			]);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Ailment($data->name, $data->description);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Ailment))
				throw $this->createEntityNotSupportedException(get_class($entity));

			$monsters = $this->entityManager->getRepository(Monster::class)->findByAilment($entity);

			foreach ($monsters as $monster)
				$monster->getAilments()->removeElement($entity);
		}
	}