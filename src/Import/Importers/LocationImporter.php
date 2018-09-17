<?php
	namespace App\Import\Importers;

	use App\Entity\Camp;
	use App\Entity\Location;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;

	class LocationImporter extends AbstractImporter {
		/**
		 * LocationImporter constructor.
		 */
		public function __construct() {
			parent::__construct(Location::class);
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return void
		 */
		public function import(EntityInterface $entity, object $data): void {
			if (!($entity instanceof Location))
				throw $this->createCannotImportException();

			$entity
				->setName($data->name)
				->setZoneCount($data->zoneCount);

			$campZones = [];

			foreach ($data->camps as $definition) {
				$campZones[] = $definition->zone;

				$camp = $entity->getCamp($definition->zone);

				if (!$camp) {
					$camp = new Camp($entity, $definition->name, $definition->zone);

					$entity->getCamps()->add($camp);
				} else
					$camp->setName($definition->name);
			}

			/** @var Camp[]|Collection $removed */
			$removed = $entity->getCamps()->matching(
				Criteria::create()
					->where(Criteria::expr()->notIn('zone', $campZones))
			);

			foreach ($removed as $item)
				$entity->getCamps()->removeElement($item);
		}

		/**
		 * @param string $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(string $id, object $data): EntityInterface {
			$location = new Location($data->name, $data->zoneCount);

			$this->import($location, $data);

			return $location;
		}
	}