<?php
	namespace App\Import;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	interface ImporterInterface {
		/**
		 * @return string
		 */
		public function getSupportedClass(): string;

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void;

		/**
		 * @param int    $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $data): EntityInterface;
	}