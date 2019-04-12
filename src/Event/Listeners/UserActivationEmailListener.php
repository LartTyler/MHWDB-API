<?php
	namespace App\Event\Listeners;

	use App\Contrib\Transformers\UserTransformer;
	use App\Entity\User;
	use App\Event\Events\ApiEntityCreateEvent;
	use App\Utility\ObjectUtil;

	class UserActivationEmailListener {
		/**
		 * @var UserTransformer
		 */
		protected $transformer;

		/**
		 * UserActivationEmailListener constructor.
		 *
		 * @param UserTransformer $transformer
		 */
		public function __construct(UserTransformer $transformer) {
			$this->transformer = $transformer;
		}

		/**
		 * @param ApiEntityCreateEvent $event
		 *
		 * @return void
		 */
		public function onUserCreate(ApiEntityCreateEvent $event): void {
			$entity = $event->getEntity();
			$payload = $event->getPayload();

			if (!($entity instanceof User) || !ObjectUtil::isset($payload, 'activationUrl'))
				return;

			$this->transformer->sendActivationEmail($entity, $payload->activationUrl);
		}
	}