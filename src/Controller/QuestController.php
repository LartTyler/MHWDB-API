<?php
	namespace App\Controller;

	use App\Contrib\Transformers\QuestTransformer;
	use App\Entity\Quest;
	use App\Entity\Quests\DeliveryQuest;
	use App\Entity\Quests\DeliveryQuestEndemicLifeTarget;
	use App\Entity\Quests\DeliveryQuestObjectTarget;
	use App\Entity\Quests\GatherQuest;
	use App\Entity\Quests\MonsterQuest;
	use App\Entity\Quests\MonsterQuestTarget;
	use App\Entity\Strings\EndemicLifeStrings;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\Strings\LocationStrings;
	use App\Entity\Strings\MonsterStrings;
	use App\Entity\Strings\QuestStrings;
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
			// TODO Normal doList() code might not work properly for inheritance mapping. Check that this won't break.
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

			$output = [
				'id' => $entity->getId(),
				'subject' => $entity->getSubject(),
				'objective' => $entity->getObjective(),
				'type' => $entity->getType(),
				'rank' => $entity->getRank(),
				'stars' => $entity->getStars(),
				'timeLimit' => $entity->getTimeLimit(),
				'maxHunters' => $entity->getMaxHunters(),
				'maxFaints' => $entity->getMaxFaints(),
			];

			if ($projection->isAllowed('name') || $projection->isAllowed('description')) {
				/** @var QuestStrings $strings */
				$strings = $this->getStrings($entity);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			if ($projection->isAllowed('location')) {
				$location = $entity->getLocation();

				$output['location'] = [
					'id' => $location->getId(),
				];

				if ($projection->isAllowed('location.name')) {
					/** @var LocationStrings $strings */
					$strings = $this->getStrings($location);

					$output['location']['name'] = $strings->getName();
				}
			}

			if ($entity instanceof GatherQuest) {
				$output['amount'] = $entity->getAmount();

				if ($projection->isAllowed('item')) {
					$item = $entity->getItem();

					$output['item'] = [
						'id' => $item->getId(),
						'rarity' => $item->getRarity(),
						'value' => $item->getValue(),
						'carryLimit' => $item->getCarryLimit(),
						'buyPrice' => $item->getBuyPrice(),
						'sellPrice' => $item->getSellPrice(),
					];

					if ($projection->isAllowed('item.name') || $projection->isAllowed('description')) {
						/** @var ItemStrings $strings */
						$strings = $this->getStrings($item);

						$output['item'] += [
							'name' => $strings->getName(),
							'description' => $strings->getDescription(),
						];
					}
				}
			} else if ($entity instanceof DeliveryQuest) {
				$output['amount'] = $entity->getAmount();

				$target = $entity->getTarget();

				if ($target instanceof DeliveryQuestObjectTarget && $projection->isAllowed('objectName')) {
					/** @var QuestStrings $strings */
					$strings = $this->getStrings($entity);

					$output['objectName'] = $strings->getObjectName();
				} else if ($target instanceof DeliveryQuestEndemicLifeTarget && $projection->isAllowed('endemicLife')) {
					$endemicLife = $target->getEndemicLife();

					$output['endemicLife'] = [
						'id' => $endemicLife->getId(),
						'type' => $endemicLife->getType(),
						'researchPointValue' => $endemicLife->getResearchPointValue(),
						'spawnConditions' => $endemicLife->getSpawnConditions(),
					];

					if (
						$projection->isAllowed('endemicLife.name') ||
						$projection->isAllowed('endemicLife.description')
					) {
						/** @var EndemicLifeStrings $strings */
						$strings = $this->getStrings($endemicLife);

						$output['endemicLife'] += [
							'name' => $strings->getName(),
							'description' => $strings->getDescription(),
						];
					}
				}
			} else if ($entity instanceof MonsterQuest) {
				if ($projection->isAllowed('targets')) {
					$output['targets'] = $entity->getTargets()->map(
						function(MonsterQuestTarget $target) use ($projection) {
							$monster = $target->getMonster();

							$output = [
								'amount' => $target->getAmount(),
								'monster' => [
									'id' => $monster->getId(),
									'type' => $monster->getType(),
									'species' => $monster->getSpecies(),
								],
							];

							if (
								$projection->isAllowed('targets.monster.name') ||
								$projection->isAllowed('targets.monster.description')
							) {
								/** @var MonsterStrings $strings */
								$strings = $this->getStrings($monster);

								$output['monster'] += [
									'name' => $strings->getName(),
									'description' => $strings->getDescription(),
								];
							}

							return $output;
						}
					)->toArray();
				}
			} else
				throw new \InvalidArgumentException(get_class($entity) . ' is not fully supported by ' . static::class);

			return $output;
		}
	}