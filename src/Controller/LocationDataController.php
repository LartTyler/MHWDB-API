<?php
	namespace App\Controller;

	use App\Entity\Camp;
	use App\Entity\Location;
	use App\QueryDocument\Projection;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\Routing\RouterInterface;

	class LocationDataController extends AbstractDataController {
		/**
		 * LocationDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Location::class);
		}

		/**
		 * @param Location|EntityInterface|null $entity
		 * @param Projection                    $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'zoneCount' => $entity->getZoneCount(),
			];

			if ($projection->isAllowed('camps')) {
				$output['camps'] = array_map(function(Camp $camp): array {
					return [
						'id' => $camp->getId(),
						'name' => $camp->getName(),
						'zone' => $camp->getZone(),
					];
				}, $entity->getCamps()->toArray());
			}

			return $output;
		}
	}