<?php
	namespace App\Security;

	use DaybreakStudios\DozeBundle\ResponderService;
	use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

	class AuthenticationExceptionListener {
		/**
		 * @var ResponderService
		 */
		protected $responder;

		/**
		 * AuthenticationExceptionListener constructor.
		 *
		 * @param ResponderService $responder
		 */
		public function __construct(ResponderService $responder) {
			$this->responder = $responder;
		}

		/**
		 * @param FilterResponseEvent $event
		 *
		 * @return void
		 */
		public function onKernelResponse(FilterResponseEvent $event) {
			$response = $event->getResponse();

			if (!($response instanceof JWTAuthenticationFailureResponse))
				return;

			$event->setResponse($this->responder->createAccessDeniedResponse());
		}
	}