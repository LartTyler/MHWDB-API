<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\MotionValue;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class MotionValueTransformer extends AbstractTransformer {
		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof MotionValue))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'weaponType'))
				$entity->setWeaponType($data->weaponType);

			if (ObjectUtil::isset($data, 'damageType'))
				$entity->setDamageType($data->damageType);

			if (ObjectUtil::isset($data, 'stun'))
				$entity->setStun($data->stun);

			if (ObjectUtil::isset($data, 'exhaust'))
				$entity->setExhaust($data->exhaust);

			if (ObjectUtil::isset($data, 'hits'))
				$entity->setHits($data->hits);
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		protected function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties($data, [
				'name',
				'weaponType',
			]);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new MotionValue($data->name, $data->weaponType);
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