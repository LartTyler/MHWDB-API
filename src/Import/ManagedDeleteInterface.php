<?php
	namespace App\Import;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	interface ManagedDeleteInterface {
		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void;
	}