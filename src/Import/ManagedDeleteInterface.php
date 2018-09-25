<?php
	namespace App\Import;

	use App\Contrib\Exceptions\DeleteFailedException;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	interface ManagedDeleteInterface {
		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 * @throws DeleteFailedException if some precondition for delete failed
		 */
		public function delete(EntityInterface $entity): void;
	}