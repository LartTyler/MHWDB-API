<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\Ailment;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MonsterResistance;
	use App\Entity\MonsterWeakness;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	class MonsterTransformer extends AbstractTransformer {
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Monster))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'type'))
				$entity->setType($data->type);

			if (ObjectUtil::isset($data, 'species'))
				$entity->setSpecies($data->species);

			if (ObjectUtil::isset($data, 'description'))
				$entity->setDescription($data->description);

			if (ObjectUtil::isset($data, 'elements'))
				$entity->setElements($data->elements);

			if (ObjectUtil::isset($data, 'ailments'))
				$this->populateFromIdArray('ailments', $entity->getAilments(), Ailment::class, $data->ailments);

			if (ObjectUtil::isset($data, 'locations'))
				$this->populateFromIdArray('locations', $entity->getLocations(), Location::class, $data->locations);

			if (ObjectUtil::isset($data, 'resistances')) {
				$entity->getResistances()->clear();

				foreach ($data->resistances as $index => $definition) {
					if (!ObjectUtil::isset($definition, 'element'))
						throw ValidationException::missingFields(['resistances[' . $index . '].element']);

					$resistance = new MonsterResistance($entity, $definition->element);
					$entity->getResistances()->add($resistance);

					if (ObjectUtil::isset($definition, 'condition'))
						$resistance->setCondition($definition->condition);
				}
			}

			if (ObjectUtil::isset($data, 'weaknesses')) {
				$entity->getWeaknesses()->clear();

				foreach ($data->weaknesses as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'element',
							'stars',
						]
					);

					if ($missing)
						throw $this->createMissingArrayFieldsException('weaknesses', $index, $missing);

					$weakness = new MonsterWeakness($entity, $definition->element, $definition->stars);
					$entity->getWeaknesses()->add($weakness);

					if (ObjectUtil::isset($definition, 'condition'))
						$weakness->setCondition($definition->condition);
				}
			}
		}

		/**
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		protected function doCreate(object $data): EntityInterface {
			$missing = ObjectUtil::getMissingProperties(
				$data,
				[
					'name',
					'type',
					'species',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Monster($data->name, $data->type, $data->species);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected function doDelete(EntityInterface $entity): void {
			// noop
		}
	}