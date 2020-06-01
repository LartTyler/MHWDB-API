<?php
	namespace App\Controller;

	use App\Contrib\Transformers\QuestTransformer;
	use App\Entity\Quest;
	use App\Entity\WorldEvent;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class QuestController extends AbstractController {
		/**
		 * QuestController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Quest::class);
		}

		/**
		 * @Route(path="/quests", methods={"GET"}, name="quests.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/quests", methods={"PUT"}, name="quests.create")
		 *
		 * @param Request          $request
		 * @param QuestTransformer $transformer
		 *
		 * @return Response
		 */
		public function create(Request $request, QuestTransformer $transformer): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/quests/{quest<\d+>}", methods={"GET"}, name="quests.read")
		 *
		 * @param Request $request
		 * @param Quest   $quest
		 *
		 * @return Response
		 */
		public function read(Request $request, Quest $quest): Response {
			return $this->respond($request, $quest);
		}

		/**
		 * @Route(path="/quests/{quest<\d+>}", methods={"PATCH"}, name="quests.update")
		 *
		 * @param Request          $request
		 * @param Quest            $quest
		 * @param QuestTransformer $transformer
		 *
		 * @return Response
		 */
		public function update(Request $request, Quest $quest, QuestTransformer $transformer): Response {
			return $this->doUpdate($transformer, $quest, $request);
		}

		/**
		 * @Route(path="/quests/{quest<\d+>}", methods={"DELETE"}, name="quests.delete")
		 *
		 * @param Quest            $quest
		 * @param QuestTransformer $transformer
		 *
		 * @return Response
		 */
		public function delete(Quest $quest, QuestTransformer $transformer): Response {
			return $this->doDelete($transformer, $quest);
		}

		/**
		 * @param EntityInterface $entity
		 * @param Projection      $projection
		 *
		 * @return array
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Quest);

			$output = $this->normalizeQuest($projection, '', $entity);

			if ($projection->isAllowed('events')) {
				$output['events'] = $entity->getEvents()->map(
					function(WorldEvent $event) use ($projection) {
						return $this->normalizeWorldEvent($projection, 'events', $event);
					}
				)->toArray();
			}

			return $output;
		}
	}