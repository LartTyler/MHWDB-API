<?php
	namespace App\Contrib\Transformers;

	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Strings\ArmorSetBonusStrings;
	use App\Localization\L10nUtil;
	use App\Utility\NullObject;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class ArmorSetBonusTransformer extends BaseTransformer {
		/**
		 * @var RequestStack
		 */
		protected $requestStack;

		/**
		 * ArmorSetBonusTransformer constructor.
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
			if (!ObjectUtil::isset($data, 'name'))
				throw ValidationException::missingFields(['name']);

			return new ArmorSetBonus();
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof ArmorSetBonus))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'name'))
				$this->getStrings($entity)->setName($data->name);

			if (ObjectUtil::isset($data, 'ranks')) {
				$entity->getRanks()->clear();

				foreach ($data->ranks as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'pieces',
							'skill',
						]
					);

					if ($missing)
						throw ValidationException::missingNestedFields('ranks', $index, $missing);

					$rank = new ArmorSetBonusRank(
						$entity,
						$definition->pieces,
						$this->getSkillRankFromSimpleSkill(
							'ranks[' . $index . ']',
							$definition->skill
						)
					);

					$entity->getRanks()->add($rank);
				}
			}
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof ArmorSetBonus))
				throw EntityTransformerException::subjectNotSupported($entity);

			/** @var ArmorSet[] $sets */
			$sets = $this->entityManager->getRepository(ArmorSet::class)->findBy(
				[
					'bonus' => $entity,
				]
			);

			foreach ($sets as $set)
				$set->setBonus(null);
		}

		/**
		 * @param ArmorSetBonus $armorSetBonus
		 *
		 * @return ArmorSetBonusStrings
		 */
		protected function getStrings(ArmorSetBonus $armorSetBonus): ArmorSetBonusStrings {
			$strings = L10nUtil::findStringsForTag(
				$lang = $this->requestStack->getCurrentRequest()->getLocale(),
				$armorSetBonus->getStrings()
			);

			if ($strings instanceof NullObject)
				$armorSetBonus->getStrings()->add($strings = new ArmorSetBonusStrings($armorSetBonus, $lang));

			return $strings;
		}
	}