<?php
	namespace App\Import\Importers;

	use App\Entity\MotionValue;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class MotionValueImporter extends AbstractImporter {
		/**
		 * MotionValueImporter constructor.
		 */
		public function __construct() {
			parent::__construct(MotionValue::class);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof MotionValue))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setWeaponType($data->weaponType)
				->setDamageType($data->damageType)
				->setExhaust($data->exhaust)
				->setStun($data->stun)
				->setHits($data->hits);
		}

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface {
			$motionValue = new MotionValue($data->name, $data->weaponType);
			$motionValue->setId($id);

			$this->import($motionValue, $data);

			return $motionValue;
		}
	}