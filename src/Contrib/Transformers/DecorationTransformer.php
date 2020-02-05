<?php
	namespace App\Contrib\Transformers;

	use App\Entity\Decoration;
	use App\Entity\Strings\DecorationStrings;
	use App\Localization\L10nUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\EntityTransformerException;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;
	use DaybreakStudios\Utility\EntityTransformers\Utility\ObjectUtil;

	class DecorationTransformer extends BaseTransformer {
		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'name',
					'slot',
					'rarity',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Decoration($data->slot, $data->rarity);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Decoration))
				throw EntityTransformerException::subjectNotSupported($entity);

			if (ObjectUtil::isset($data, 'name'))
				$this->getStrings($entity)->setName($data->name);

			if (ObjectUtil::isset($data, 'slot'))
				$entity->setSlot($data->slot);

			if (ObjectUtil::isset($data, 'rarity'))
				$entity->setRarity($data->rarity);

			if (ObjectUtil::isset($data, 'skills'))
				$this->populateFromSimpleSkillsArray('skills', $entity->getSkills(), $data->skills);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function doDelete(EntityInterface $entity): void {
			// noop
		}

		/**
		 * @param Decoration $decoration
		 *
		 * @return DecorationStrings
		 */
		protected function getStrings(Decoration $decoration): DecorationStrings {
			$strings = L10nUtil::findOrCreateStrings($this->getCurrentLocale(), $decoration);
			assert($strings instanceof DecorationStrings);

			return $strings;
		}
	}