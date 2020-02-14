<?php
	namespace App\Contrib\Transformers;

	use App\Entity\EndemicLife;
	use App\Entity\Location;
	use App\Entity\Strings\EndemicLifeStrings;
	use App\Localization\L10nUtil;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\EntityTransformers\Exceptions\ValidationException;

	class EndemicLifeTransformer extends BaseTransformer {
		/**
		 * {@inheritdoc}
		 */
		public function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'name',
					'type',
					'researchPointValue',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new EndemicLife($data->type, $data->researchPointValue);
		}

		/**
		 * {@inheritdoc}
		 */
		public function doUpdate(EntityInterface $entity, object $data): void {
			assert($entity instanceof EndemicLife);

			if (ObjectUtil::isset($data, 'name'))
				$this->getStrings($entity)->setName($data->name);

			if (ObjectUtil::isset($data, 'description'))
				$this->getStrings($entity)->setDescription($data->description);

			if (ObjectUtil::isset($data, 'type'))
				$entity->setType($data->type);

			if (ObjectUtil::isset($data, 'researchPointValue'))
				$entity->setResearchPointValue($data->researchPointValue);

			if (ObjectUtil::isset($data, 'spawnConditions'))
				$entity->setSpawnConditions($data->spawnConditions);

			if (ObjectUtil::isset($data, 'locations'))
				$this->populateFromIdArray('locations', $entity->getLocations(), Location::class, $data->locations);
		}

		/**
		 * {@inheritdoc}
		 */
		public function doDelete(EntityInterface $entity): void {
			// TODO: Implement doDelete() method.
		}

		/**
		 * @param EndemicLife $endemicLife
		 *
		 * @return EndemicLifeStrings
		 */
		protected function getStrings(EndemicLife $endemicLife): EndemicLifeStrings {
			$strings = L10nUtil::findOrCreateStrings($this->getCurrentLocale(), $endemicLife);
			assert($strings instanceof EndemicLifeStrings);

			return $strings;
		}
	}