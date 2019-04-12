<?php
	namespace App\Event\Events;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Component\EventDispatcher\Event;

	abstract class ApiEntityEvent extends Event {
		/**
		 * @var EntityInterface
		 */
		protected $entity;

		/**
		 * ApiEntityEvent constructor.
		 *
		 * @param EntityInterface $entity
		 */
		public function __construct(EntityInterface $entity) {
			$this->entity = $entity;
		}

		/**
		 * @return EntityInterface
		 */
		public function getEntity(): EntityInterface {
			return $this->entity;
		}
	}