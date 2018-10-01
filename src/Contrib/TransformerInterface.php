<?php
	namespace App\Contrib;

	use App\Contrib\Exceptions\IntegrityException;
	use App\Contrib\Exceptions\ValidationException;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	interface TransformerInterface {
		/**
		 * Creates a new entity from the provided values. Please note that this method will persist the entity to
		 * Doctrine, but will NOT flush the changes.
		 *
		 * If any of the following exceptions are thrown, no changes will be written to the database.
		 *
		 * A {@see ValidationException} will be thrown if any of the following conditions are met.
		 * 		- Any value does not match it's expected type
		 *
		 * An {@see IntegrityException} will be thrown if any of the following conditions are met.
		 * 		- Creating the entity would result in a duplication unique value being written to the database
		 * 		- A reference to another entity in the API could not be resolved
		 *
		 * @param object $data
		 *
		 * @return EntityInterface
		 * @throws ValidationException
		 * @throws IntegrityException
		 */
		public function create(object $data): EntityInterface;

		/**
		 * Updates an existing entity using the provided values. Please note that this method will NOT flush
		 * any changes.
		 *
		 * If any of the following exceptions are thrown, no changes will be written to the database.
		 *
		 * A {@see ValidationException} will be thrown if any of the following conditions are met.
		 * 		- Any value does not match it's expected type
		 *
		 * An {@see IntegrityException} will be thrown if any of the following conditions are met.
		 * 		- Creating the entity would result in a duplication unique value being written to the database
		 * 		- A reference to another entity in the API could not be resolved
		 *
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 * @throws ValidationException
		 * @throws IntegrityException
		 */
		public function update(EntityInterface $entity, object $data): void;

		/**
		 * Deletes the entity from the database. Please note that this method will only mark the entity for removal
		 * in Doctrine, and will NOT flush any changes.
		 *
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void;
	}