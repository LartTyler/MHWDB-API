<?php
	namespace App\Controller;

	use App\Contrib\ApiErrors\InvalidParameterError;
	use App\Contrib\ApiErrors\InvalidPayloadError;
	use App\Contrib\ApiErrors\MissingRequiredFieldsError;
	use App\Contrib\Transformers\UserTransformer;
	use App\Entity\User;
	use App\Entity\UserRole;
	use App\QueryDocument\Projection;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Doze\Errors\NotFoundError;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

	class UserController extends AbstractController {
		/**
		 * UserController constructor.
		 */
		public function __construct() {
			parent::__construct(User::class);
		}

		/**
		 * @Route(path="/users", methods={"GET"}, name="users.list")
		 * @IsGranted("ROLE_ADMIN")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/users", methods={"PUT"}, name="users.create")
		 * @IsGranted("ROLE_ADMIN")
		 *
		 * @param UserTransformer $transformer
		 * @param Request         $request
		 *
		 * @return Response
		 */
		public function create(UserTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/users/{user<\d+>}", methods={"GET"}, name="users.read")
		 * @IsGranted("ROLE_ADMIN")
		 *
		 * @param User $user
		 *
		 * @return Response
		 */
		public function read(User $user): Response {
			return $this->respond($user);
		}

		/**
		 * @Route(path="/users/{user<\d+>}", methods={"PATCH"}, name="users.update")
		 * @IsGranted("ROLE_ADMIN")
		 *
		 * @param User            $user
		 * @param UserTransformer $transformer
		 * @param Request         $request
		 *
		 * @return Response
		 */
		public function update(User $user, UserTransformer $transformer, Request $request): Response {
			return $this->doUpdate($transformer, $user, $request);
		}

		/**
		 * @Route(path="/users/{user<\d+>}", methods={"DELETE"}, name="users.delete")
		 * @IsGranted("ROLE_ADMIN")
		 *
		 * @param User            $user
		 * @param UserTransformer $transformer
		 *
		 * @return Response
		 */
		public function delete(User $user, UserTransformer $transformer) {
			return $this->doDelete($transformer, $user);
		}

		/**
		 * @Route(path="/users/activate/{code}", methods={"POST"}, name="users.activate")
		 *
		 * @param string                       $code
		 * @param Request                      $request
		 * @param UserPasswordEncoderInterface $encoder
		 *
		 * @return Response
		 */
		public function activate(string $code, Request $request, UserPasswordEncoderInterface $encoder): Response {
			$payload = json_decode($request->getContent());

			if (json_last_error() !== JSON_ERROR_NONE)
				return $this->respond(new InvalidPayloadError());
			else if (!ObjectUtil::isset($payload, 'password'))
				return $this->respond(new MissingRequiredFieldsError(['password']));

			$password = $payload->password;

			if (strlen($password) < 5)
				return $this->respond(new InvalidParameterError('password', 'at least 5 characters long'));

			/** @var User|null $user */
			$user = $this->entityManager->getRepository(User::class)->findOneBy(
				[
					'activationCode' => $code,
				]
			);

			if (!$user)
				return $this->respond(new NotFoundError());

			$user
				->setActivationCode(null)
				->setPassword($encoder->encodePassword($user, $password));

			$this->entityManager->flush();

			return $this->respond($user);
		}

		/**
		 * @param User|EntityInterface|null $entity
		 * @param Projection                $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
				'createdDate' => $entity->getCreatedDate()->format(\DateTime::ISO8601),
				'disabled' => $entity->isDisabled(),
				'displayName' => $entity->getDisplayName(),
				'email' => $entity->getEmail(),
			];

			if ($projection->isAllowed('roles')) {
				$output['roles'] = array_map(
					function(UserRole $role): string {
						return $role->getRole();
					},
					$entity->getRoles()
				);
			}

			return $output;
		}
	}