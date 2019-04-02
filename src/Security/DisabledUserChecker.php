<?php
	namespace App\Security;

	use App\Entity\User;
	use Symfony\Component\Security\Core\Exception\DisabledException;
	use Symfony\Component\Security\Core\User\UserCheckerInterface;
	use Symfony\Component\Security\Core\User\UserInterface;

	class DisabledUserChecker implements UserCheckerInterface {
		/**
		 * {@inheritdoc}
		 */
		public function checkPreAuth(UserInterface $user) {
			// noop
		}

		/**
		 * {@inheritdoc}
		 */
		public function checkPostAuth(UserInterface $user) {
			if (!($user instanceof User))
				return;

			if ($user->isDisabled())
				throw new DisabledException();
		}
	}