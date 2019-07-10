<?php
	namespace App\Controller;

	use App\Entity\Camp;
	use App\Entity\WorldEvent;
	use App\QueryDocument\Projection;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class EventController extends AbstractController {
		/**
		 * EventController constructor.
		 */
		public function __construct() {
			parent::__construct(WorldEvent::class);
		}

		/**
		 * @Route(path="/events", methods={"GET"}, name="events.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/events/{worldEvent<\d+>}", methods={"GET"}, name="events.read")
		 *
		 * @param WorldEvent $worldEvent
		 *
		 * @return Response
		 */
		public function read(WorldEvent $worldEvent): Response {
			return $this->respond($worldEvent);
		}

		/**
		 * @param EntityInterface|WorldEvent|null $entity
		 * @param Projection                      $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'platform' => $entity->getPlatform(),
				'exclusive' => $entity->getExclusive(),
				'type' => $entity->getType(),
				'description' => $entity->getDescription(),
				'requirements' => $entity->getRequirements(),
				'questRank' => $entity->getQuestRank(),
				'successConditions' => $entity->getSuccessConditions(),
				'startTimestamp' => $entity->getStartTimestamp()->format(\DateTime::ISO8601),
				'endTimestamp' => $entity->getEndTimestamp()->format(\DateTime::ISO8601),
			];

			if ($projection->isAllowed('location')) {
				$location = $entity->getLocation();

				$output['location'] = [
					'id' => $location->getId(),
					'name' => $location->getName(),
					'zoneCount' => $location->getZoneCount(),
				];

				if ($projection->isAllowed('location.camps')) {
					$output['location']['camps'] = $location->getCamps()->map(
						function(Camp $camp): array {
							return [
								'id' => $camp->getId(),
								'name' => $camp->getName(),
								'zone' => $camp->getZone(),
							];
						}
					)->toArray();
				}
			}

			return $output;
		}
	}