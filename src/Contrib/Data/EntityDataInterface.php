<?php
	namespace App\Contrib\Data;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	/**
	 * An updater represents a single API object in memory that's waiting to be written to the data repository.
	 *
	 * Interface UpdaterInterface
	 *
	 * @package App\Contrib\Updater
	 */
	interface EntityDataInterface {
		/**
		 * Loads values present in $data into the updater.
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
		 * Instantiates the updater using data from the data repository. Be aware that certain assumptions about $source
		 * are made and not checked, such as presence of most (if not all) fields.
		 *
		 * @param object $source
		 *
		 * @return static
		 */
		public static function fromJson(object $source);

		/**
		 * Instantiates the updater using an entity.
		 *
		 * @param EntityInterface $entity
		 *
		 * @return static
		 */
		public static function fromEntity(EntityInterface $entity);
	}