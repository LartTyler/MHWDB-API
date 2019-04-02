<?php
	namespace App\Event\Events;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class ApiEntityUpdateEvent extends ApiEntityEvent {
		public const NAME = 'api.entity.update';

		/**
		 * @var object
		 */
		protected $payload;

		/**
		 * ApiEntityUpdateEvent constructor.
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