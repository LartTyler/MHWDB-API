<?php
	namespace App\Controller;

	use App\Entity\User;
	use DaybreakStudios\RestApiCommon\Controller\AbstractApiController;
	use Symfony\Component\HttpFoundation\RequestStack;

	abstract class AbstractController extends AbstractApiController {
		/**
		 * @var RequestStack|null
		 */
		protected $requestStack = null;

		/**
		 * @required
		 *
		 * @param RequestStack $requestStack
		 *
		 * @return void
		 */
		public function setRequestStack(RequestStack $requestStack): void {
			$this->requestStack = $requestStack;
		}

		/**
		 * @return User
		 */
		protected function getUser(): User {
			$user = parent::getUser();
			assert($user instanceof User);

			return $user;
		}
	}