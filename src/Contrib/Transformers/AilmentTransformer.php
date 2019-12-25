<?php
	namespace App\Contrib\Transformers;

	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Monster;
	use App\Entity\Skill;
	use App\Entity\Strings\AilmentStrings;
	use App\Localization\L10nUtil;
	use App\Utility\NullObject;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class AilmentTransformer extends BaseTransformer {
		/**
		 * @var RequestStack
		 */
		protected $requestStack;

		/**
		 * AilmentTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface     $validator
		 * @param RequestStack           $requestStack
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			ValidatorInterface $validator,
			RequestStack $requestStack
		) {
			parent::__construct($entityManager, $validator);

			$this->requestStack = $requestStack;
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'strings',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Ailment();
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Ailment))
				throw EntityTransformerException::subjectNotSupported($entity);

			$strings = L10nUtil::findStringsForTag(
				$lang = $this->requestStack->getCurrentRequest()->getLocale(),
				$entity->getStrings()
			);

			if ($strings instanceof NullObject)
				$entity->getStrings()->add($strings = new AilmentStrings($entity, $lang));

			if (ObjectUtil::isset($data, 'name'))
				$strings->setName($data->name);

			if (ObjectUtil::isset($data, 'description'))
				$strings->setDescription($data->description);

			if (ObjectUtil::isset($data, 'recovery')) {
				$recovery = $entity->getRecovery();
				$definition = $data->recovery;

				if (ObjectUtil::isset($definition, 'items')) {
					$this->populateFromIdArray(
						'recovery.items',
						$recovery->getItems(),
						Item::class,
						$definition->items
					);
				}

				if (ObjectUtil::isset($definition, 'actions'))
					$recovery->setActions($definition->actions);
			}

			if (ObjectUtil::isset($data, 'protection')) {
				$protection = $entity->getProtection();
				$definition = $data->protection;

				if (ObjectUtil::isset($definition, 'items')) {
					$this->populateFromIdArray(
						'protection.items',
						$protection->getItems(),
						Item::class,
						$definition->items
					);
				}

				if (ObjectUtil::isset($definition, 'skills')) {
					$this->populateFromIdArray(
						'protection.skills',
						$protection->getSkills(),
						Skill::class,
						$definition->skills
					);
				}
			}
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Ailment))
				throw EntityTransformerException::subjectNotSupported($entity);

			/** @var Monster[] $monsters */
			$monsters = $this->entityManager->getRepository(Monster::class)->findByAilment($entity);

			foreach ($monsters as $monster)
				$monster->getAilments()->removeElement($entity);
		}
	}