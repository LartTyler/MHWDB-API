<?php
	namespace App\Controller;

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

			$output = $this->normalizeWorldEvent($projection, '', $entity);

			if ($projection->isAllowed('quest'))
				$output['quest'] = $this->normalizeQuest($projection, 'quest', $entity->getQuest());

			return $output;
		}
	}