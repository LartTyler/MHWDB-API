<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\Decoration;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class DecorationTransformer extends AbstractTransformer {
		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Decoration))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'slot'))
				$entity->setSlot($data->slot);

			if (ObjectUtil::isset($data, 'rarity'))
				$entity->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'skills'))
				$this->populateFromSimpleSkillsArray('skills', $entity->getSkills(), $data->skills);
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
					'slot',
					'rarity',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Decoration($data->name, $data->slot, $data->rarity);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected function doDelete(EntityInterface $entity): void {
			// noop
		}
	}