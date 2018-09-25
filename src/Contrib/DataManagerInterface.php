<?php
	namespace App\Contrib;

	use App\Contrib\Exceptions\DeleteFailedException;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	interface DataManagerInterface {
		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function export(EntityInterface $entity): void;

		/**
		 * @param int|null $id
		 * @param object   $input
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $input): EntityInterface;

		/**
		 * @param EntityInterface $entity
		 * @param object          $input
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $input): void;

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 * @throws DeleteFailedException if some precondition for the delete failed
		 */
		public function delete(EntityInterface $entity): void;

		/**
		 * @return string
		 */
		public function getEntityClass(): string;
	}