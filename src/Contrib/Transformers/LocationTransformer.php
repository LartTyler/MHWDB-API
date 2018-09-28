<?php
	namespace App\Contrib\Transformers;

	use App\Contrib\Exceptions\ValidationException;
	use App\Entity\Camp;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Utility\ObjectUtil;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Criteria;

	class LocationTransformer extends AbstractTransformer {
		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Location))
				throw $this->createEntityNotSupportedException(get_class($entity));

			if (ObjectUtil::isset($data, 'name'))
				$entity->setName($data->name);

			if (ObjectUtil::isset($data, 'zoneCount'))
				$entity->setZoneCount($data->zoneCount);

			if (ObjectUtil::isset($data, 'camps')) {
				$zones = [];

				foreach ($data->camps as $index => $definition) {
					$missing = ObjectUtil::getMissingProperties(
						$definition,
						[
							'name',
							'zone',
						]
					);

					if ($missing) {
						throw ValidationException::missingFields(
							array_map(
								function(string $key) use ($index): string {
									return 'camps[' . $index . '].' . $key;
								},
								$missing
							)
						);
					} else if ($definition->zone > $entity->getZoneCount()) {
						throw ValidationException::invalidFieldValue(
							'camps[' . $index . '].zone',
							'The location only has ' . $entity->getZoneCount() . ' zone(s)'
						);
					}

					$zones[] = $definition->zone;

					$camp = $entity->getCamp($definition->zone);

					if (!$camp)
						$entity->getCamps()->add(new Camp($entity, $definition->name, $definition->zone));
					else
						$camp->setName($definition->name);
				}

				if ($zones) {
					$matching = $entity->getCamps()->matching(
						Criteria::create()
							->where(Criteria::expr()->notIn('zone', $zones))
					);

					foreach ($matching as $item)
						$entity->getCamps()->removeElement($item);
				} else
					$entity->getCamps()->clear();
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
					'zoneCount',
				]
			);

			if ($missing)
				throw ValidationException::missingFields($missing);

			return new Location($data->name, $data->zoneCount);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		protected function doDelete(EntityInterface $entity): void {
			if (!($entity instanceof Location))
				throw $this->createEntityNotSupportedException(get_class($entity));

			$monsters = $this->entityManager->getRepository(Monster::class)->findByLocation($entity);

			foreach ($monsters as $monster)
				$monster->getLocations()->removeElement($entity);
		}
	}