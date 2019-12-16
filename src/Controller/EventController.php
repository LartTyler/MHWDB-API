<?php
	namespace App\Controller;

	use App\Entity\Camp;
	use App\Entity\WorldEvent;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class EventController extends AbstractController {
		/**
		 * EventController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, WorldEvent::class);
		}

		/**
		 * @Route(path="/events", methods={"GET"}, name="events.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/events/{worldEvent<\d+>}", methods={"GET"}, name="events.read")
		 *
		 * @param Request    $request
		 * @param WorldEvent $worldEvent
		 *
		 * @return Response
		 */
		public function read(Request $request, WorldEvent $worldEvent): Response {
			return $this->respond($request, $worldEvent);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof WorldEvent);

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'platform' => $entity->getPlatform(),
				'exclusive' => $entity->getExclusive(),
				'type' => $entity->getType(),
				'expansion' => $entity->getExpansion(),
				'description' => $entity->getDescription(),
				'requirements' => $entity->getRequirements(),
				'questRank' => $entity->getQuestRank(),
				'masterRank' => $entity->isMasterRank(),
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