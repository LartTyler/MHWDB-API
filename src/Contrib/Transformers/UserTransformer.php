<?php
	namespace App\Contrib\Transformers;

	use App\Entity\User;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\AbstractEntityTransformer;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\IntegrityException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;

	class UserTransformer extends AbstractEntityTransformer {
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
	}