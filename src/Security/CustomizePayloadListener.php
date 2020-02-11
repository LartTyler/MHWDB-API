<?php
	namespace App\Security;

	use App\Entity\User;
	use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

	class CustomizePayloadListener {
		/**
		 * @param JWTCreatedEvent $event
		 *
		 * @return void
		 */
		public function onJwtCreate(JWTCreatedEvent $event): void {
			$user = $event->getUser();

			if (!($user instanceof User))
				return;

			$payload = $event->getData();

			$payload['displayName'] = $user->getDisplayName();
			$payload['roles'] = $user->getRoles();

			$event->setData($payload);
		}
	}