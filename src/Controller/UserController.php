<?php
	namespace App\Controller;

	use App\Contrib\ApiErrors\InvalidParameterError;
	use App\Contrib\ApiErrors\MissingRequiredFieldsError;
	use App\Contrib\Transformers\UserTransformer;
	use App\Entity\User;
	use App\Entity\UserRole;
	use App\Response\NoContentResponse;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\RestApiCommon\Error\Errors\ApiController\InvalidPayloadError;
	use DaybreakStudios\RestApiCommon\Error\Errors\NotFoundError;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

	class UserController extends AbstractController {
		/**
		 * UserController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, User::class);
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
			return $this->doList($request);
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
		 * @param Request $request
		 * @param User    $user
		 *
		 * @return Response
		 */
		public function read(Request $request, User $user): Response {
			return $this->respond($request, $user);
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
			$payload = @json_decode($request->getContent());

			if (json_last_error() !== JSON_ERROR_NONE)
				return $this->respond($request, new InvalidPayloadError());
			else if (!ObjectUtil::isset($payload, 'password'))
				return $this->respond($request, new MissingRequiredFieldsError(['password']));

			$password = $payload->password;

			if (strlen($password) < 5)
				return $this->respond($request, new InvalidParameterError('password', 'at least 5 characters long'));

			/** @var User|null $user */
			$user = $this->entityManager->getRepository(User::class)->findOneBy(
				[
					'activationCode' => $code,
				]
			);

			if (!$user)
				return $this->respond($request, new NotFoundError());

			$user
				->setActivationCode(null)
				->setPassword($encoder->encodePassword($user, $password));

			$this->entityManager->flush();

			return $this->respond($request, $user);
		}

		/**
		 * @Route(path="/users/password-reset", methods={"POST"}, name="users.password-reset.send-code")
		 *
		 * @param UserTransformer $transformer
		 * @param Request         $request
		 *
		 * @return Response
		 */
		public function sendPasswordResetCode(UserTransformer $transformer, Request $request): Response {
			$payload = json_decode($request->getContent());

			if (json_last_error() !== JSON_ERROR_NONE)
				return $this->respond($request, new InvalidPayloadError());

			$missing = ObjectUtil::getMissingProperties(
				$payload,
				[
					'email',
					'passwordResetUrl',
				]
			);

			if ($missing)
				return $this->respond($request, new MissingRequiredFieldsError($missing));

			/** @var User|null $user */
			$user = $this->entityManager->getRepository(User::class)->findOneBy(
				[
					'email' => $payload->email,
				]
			);

			if (!$user)
				return new NoContentResponse();

			$transformer->sendPasswordResetEmail($user, $payload->passwordResetUrl);

			$this->entityManager->flush();

			return new NoContentResponse();
		}

		/**
		 * @Route(path="/users/password-reset/{code}", methods={"POST"}, name="users.password-reset.reset")
		 *
		 * @param string                       $code
		 * @param Request                      $request
		 * @param UserPasswordEncoderInterface $encoder
		 *
		 * @return Response
		 */
		public function resetPassword(string $code, Request $request, UserPasswordEncoderInterface $encoder): Response {
			$payload = @json_decode($request->getContent());

			if (json_last_error() !== JSON_ERROR_NONE)
				return $this->respond($request, new InvalidPayloadError());
			else if (!ObjectUtil::isset($payload, 'password'))
				return $this->respond($request, new MissingRequiredFieldsError(['password']));

			$password = $payload->password;

			if (strlen($password) < 5)
				return $this->respond($request, new InvalidParameterError('password', 'at least 5 characters long'));

			/** @var User|null $user */
			$user = $this->entityManager->getRepository(User::class)->findOneBy(
				[
					'passwordResetCode' => $code,
				]
			);

			if (!$user)
				return $this->respond($request, new NotFoundError());

			$user
				->setPasswordResetCode(null)
				->setPassword($encoder->encodePassword($user, $password));

			$this->entityManager->flush();

			return new NoContentResponse();
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof User);

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