<?php
	namespace App\Contrib\Data;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * An entity data object represents a single API object in memory that's waiting to be written to the data
	 * repository, or loaded from the data repository into the database.
	 *
	 * Interface EntityDataInterface
	 *
	 * @package App\Contrib\Data
	 */
	interface EntityDataInterface {
		/**
		 * Loads values present in $data into the object, usually provided by a user through the contrib API.
		 *
		 * @param object $data
		 *
		 * @return void
		 */
		public function update(object $data): void;

		/**
		 * @return array
		 */
		public function normalize(): array;

		/**
		 * @param bool $short if true, return the group name without the top-level group name.
		 *
		 * @return string|null
		 */
		public function getEntityGroupName(bool $short = false): ?string;

		/**
		 * Instantiates the entity data using data from the data repository. Be aware that certain assumptions about
		 * $source are made and not checked for sanity, such as presence of most (if not all) fields.
		 *
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source);

		/**
		 * Instantiates the entity data using an entity from the database.
		 *
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity);
	}