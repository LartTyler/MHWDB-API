<?php
	namespace App\Controller;

	use App\Entity\User;
	use App\Localization\L10nUtil;
	use App\Localization\TranslatableEntityInterface;
	use App\Utility\NullObject;
	use DaybreakStudios\RestApiCommon\Controller\AbstractApiController;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
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
		 * @param TranslatableEntityInterface $entity
		 *
		 * @return NullObject|EntityInterface
		 */
		protected function getStrings(TranslatableEntityInterface $entity): object {
			return NullObject::of(
				L10nUtil::findStrings($this->requestStack->getCurrentRequest()->getLocale(), $entity)
			);
		}
	}