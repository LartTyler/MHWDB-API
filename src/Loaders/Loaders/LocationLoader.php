<?php
	namespace App\Loaders\Loaders;

	use App\Entity\Camp;
	use App\Entity\Location;
	use App\Loaders\Schemas\LocationSchema;
	use App\Loaders\Type;
	use Doctrine\ORM\EntityManagerInterface;

	class LocationLoader extends AbstractLoader {
		protected $manager;

		/**
		 * LocationLoader constructor.
		 *
		 * @param EntityManagerInterface $manager
		 * @param string                 $path
		 */
		public function __construct(EntityManagerInterface $manager, string $path) {
			parent::__construct(Type::LOCATIONS, $path, LocationSchema::class);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function load(array $context): void {
			$locationSchemas = $this->read();

			foreach ($locationSchemas as $locationSchema) {
				$location = $this->manager->getRepository(Location::class)->findOneBy([
					'name' => $locationSchema->getName(),
				]);

				if (!$location) {
					$location = new Location($locationSchema->getName(), $locationSchema->getZoneCount());

					$this->manager->persist($location);
				} else
					$location->setZoneCount($locationSchema->getZoneCount());

				foreach ($locationSchema->getCamps() as $campSchema) {
					$camp = $location->getCamp($campSchema->getZone());

					if (!$camp) {
						$camp = new Camp($location, $campSchema->getName(), $campSchema->getZone());

						$location->getCamps()->add($camp);
					} else
						$camp->setName($campSchema->getName());
				}
			}

			$this->manager->flush();
		}

		/**
		 * @return LocationSchema[]
		 */
		protected function read(): array {
			return parent::read();
		}
	}