<?php
	namespace App\Security;

	use App\Response\AccessDeniedError;
	use DaybreakStudios\DozeBundle\ResponderService;
	use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
	use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
	use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
	use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;

	class AuthenticationFailureListener {
		/**
		 * @var ResponderService
		 */
		protected $responderService;

		/**
		 * AuthenticationFailureListener constructor.
		 *
		 * @param ResponderService $responderService
		 */
		public function __construct(ResponderService $responderService) {
			$this->responderService = $responderService;
		}

		/**
		 * @param AuthenticationFailureEvent $event
		 *
		 * @return void
		 */
		public function onAuthenticationFailure(AuthenticationFailureEvent $event): void {
			$error = new AccessDeniedError('Credentials not found, please verify your username and password');

			$event->setResponse($this->responderService->createErrorResponse($error));
		}

		/**
		 * @param JWTInvalidEvent $event
		 *
		 * @return void
		 */
		public function onInvalidToken(JWTInvalidEvent $event): void {
			$error = new AccessDeniedError('Your token is invalid, please login again to get a new one');

			$event->setResponse($this->responderService->createErrorResponse($error));
		}

		/**
		 * @param JWTNotFoundEvent $event
		 *
		 * @return void
		 */
		public function onTokenNotFound(JWTNotFoundEvent $event): void {
			$error = new AccessDeniedError('You must pass a token in the Authorization header to access this resource');

			$event->setResponse($this->responderService->createErrorResponse($error));
		}

		/**
		 * @param JWTExpiredEvent $event
		 *
		 * @return void
		 */
		public function onTokenExpired(JWTExpiredEvent $event): void {
			$error = new AccessDeniedError('Your token is expired, please login again to get a new one');

			$event->setResponse($this->responderService->createErrorResponse($error));
		}
	}