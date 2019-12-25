<?php
	namespace App\Controller;

	use App\Entity\User;
	use App\Localization\L10nUtil;
	use App\Utility\NullObject;
	use DaybreakStudios\RestApiCommon\Controller\AbstractApiController;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Selectable;
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

		/**
		 * @param Selectable $strings
		 *
		 * @return NullObject|EntityInterface
		 */
		protected function getStrings(Selectable $strings): object {
			return L10nUtil::findStringsForTag($this->requestStack->getCurrentRequest()->getLocale(), $strings);
		}
	}