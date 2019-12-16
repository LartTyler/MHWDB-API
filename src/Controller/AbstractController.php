<?php
	namespace App\Controller;

	use App\Entity\User;
	use DaybreakStudios\RestApiCommon\Controller\AbstractApiController;

	abstract class AbstractController extends AbstractApiController {
		/**
		 * @return User
		 */
		protected function getUser(): User {
			$user = parent::getUser();
			assert($user instanceof User);

			return $user;
		}
	}