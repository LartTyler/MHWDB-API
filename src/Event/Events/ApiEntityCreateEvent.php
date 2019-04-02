<?php
	namespace App\Event\Events;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ApiEntityCreateEvent extends ApiEntityEvent {
		public const NAME = 'api.entity.create';

		/**
		 * @var object
		 */
		protected $payload;

		/**
		 * EntityCreateEvent constructor.
		 *
		 * @param EntityInterface $entity
		 * @param object          $payload
		 */
		public function __construct(EntityInterface $entity, object $payload) {
			parent::__construct($entity);

			$this->payload = $payload;
		}

		/**
		 * @return object
		 */
		public function getPayload(): object {
			return $this->payload;
		}
	}