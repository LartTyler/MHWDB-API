<?php
	namespace App\Contrib\Transformers;

	use App\Entity\User;
	use App\Utility\ObjectUtil;
	use App\Utility\StringUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\AbstractEntityTransformer;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Templating\EngineInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class UserTransformer extends AbstractEntityTransformer {
		/**
		 * @var \Swift_Mailer
		 */
		protected $mailer;

		/**
		 * @var EngineInterface
		 */
		protected $templater;

		/**
		 * UserTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface     $validator
		 * @param \Swift_Mailer          $mailer
		 * @param EngineInterface        $templater
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			ValidatorInterface $validator,
			\Swift_Mailer $mailer,
			EngineInterface $templater
		) {
			parent::__construct($entityManager, $validator);

			$this->mailer = $mailer;
			$this->templater = $templater;
		}

		/**
		 * {@inheritdoc}
		 */
		public function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'email',
					'displayName',
				]
			);

			if ($missing)
				ValidationException::missingFields($missing);

			return new User($data->email, $data->displayName);
		}

		/**
		 * {@inheritdoc}
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof User))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'email')) {
				/** @var User|null $check */
				$check = $this->entityManager->getRepository(User::class)->findOneBy(
					[
						'email' => $data->email,
					]
				);

				if ($check && $check !== $entity)
					throw IntegrityException::duplicateUniqueValue('email', $check->getId());
				else
					$entity->setEmail($data->email);
			}

			if (ObjectUtil::isset($data, 'displayName')) {
				/** @var User|null $check */
				$check = $this->entityManager->getRepository(User::class)->findOneBy(
					[
						'displayName' => $data->displayName,
					]
				);

				if ($check && $check !== $entity)
					throw IntegrityException::duplicateUniqueValue('displayName', $check->getId());
				else
					$entity->setDisplayName($data->displayName);
			}

			if (ObjectUtil::isset($data, 'roles'))
				$entity->setRoles($data->roles);
		}

		/**
		 * {@inheritdoc}
		 */
		public function doDelete(EntityInterface $entity): void {
			// noop
		}

		/**
		 * @param User   $user
		 * @param string $url
		 * @param bool   $resetCode
		 *
		 * @return void
		 */
		public function sendActivationEmail(User $user, string $url, bool $resetCode = true): void {
			if ($resetCode || !$user->getActivationCode())
				$user->setActivationCode(bin2hex(random_bytes(32)));

			$url = StringUtil::interpolate(
				$url,
				[
					'code' => $user->getActivationCode(),
				]
			);

			$message = (new \Swift_Message('Activate Your Account'))
				->setFrom('no-reply@mail.mhw-db.com', 'MHWDB Contrib')
				->setTo($user->getEmail())
				->setBody(
					$this->templater->render(
						'activation-email.html.twig',
						[
							'activationUrl' => $url,
						]
					),
					'text/html'
				);

			$this->mailer->send($message);
		}

		/**
		 * @param User   $user
		 * @param string $url
		 * @param bool   $resetCode
		 *
		 * @return void
		 */
		public function sendPasswordResetEmail(User $user, string $url, bool $resetCode = true): void {
			if ($resetCode || !$user->getPasswordResetCode())
				$user->setPasswordResetCode(bin2hex(random_bytes(32)));

			$url = StringUtil::interpolate(
				$url,
				[
					'code' => $user->getPasswordResetCode(),
				]
			);

			$message = (new \Swift_Message('Password Resest Request'))
				->setFrom('no-reply@mail.mhw-db.com', 'MHWDB Contrib')
				->setTo($user->getEmail())
				->setBody(
					$this->templater->render(
						'password-reset-email.html.twig',
						[
							'passwordResetUrl' => $url,
						]
					),
					'text/html'
				);

			$this->mailer->send($message);
		}
	}