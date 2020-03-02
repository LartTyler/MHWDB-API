<?php
	namespace App\Controller;

	use App\Contrib\Transformers\QuestTransformer;
	use App\Entity\Location;
	use App\Entity\MonsterQuestTarget;
	use App\Entity\Quest;
	use App\Entity\QuestReward;
	use App\Entity\RewardCondition;
	use App\Entity\Strings\EndemicLifeStrings;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\Strings\LocationStrings;
	use App\Entity\Strings\MonsterStrings;
	use App\Entity\Strings\QuestStrings;
	use App\Game\Quest\DeliveryType;
	use App\Game\Quest\QuestObjective;
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

			$output = [
				'id' => $entity->getId(),
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

			if ($projection->isAllowed('rewards')) {
				$output['rewards'] = $entity->getRewards()->map(
					function(QuestReward $reward) use ($projection) {
						$output = [];

						if ($projection->isAllowed('rewards.item'))
							$output['item'] = $this->normalizeItem($projection, 'rewards.item', $reward->getItem());

						if ($projection->isAllowed('rewards.conditions'))
							$output['conditions'] = $reward->getConditions()->map(
								function(RewardCondition $condition) {
									return $this->normalizeRewardCondition($condition);
								}
							)->toArray();

						return $output;
					}
				)->toArray();
			}

			switch ($entity->getObjective()) {
				case QuestObjective::GATHER:
					$item = $entity->getItem();

					$output += [
						'amount' => $entity->getAmount(),
						'item' => [
							'id' => $item->getId(),
							'rarity' => $item->getRarity(),
							'value' => $item->getValue(),
							'carryLimit' => $item->getCarryLimit(),
							'buyPrice' => $item->getBuyPrice(),
							'sellPrice' => $item->getSellPrice(),
						],
					];

					if ($projection->isAllowed('item.name') || $projection->isAllowed('item.description')) {
						/** @var ItemStrings $strings */
						$strings = $this->getStrings($item);

						$output['item'] += [
							'name' => $strings->getName(),
							'description' => $strings->getDescription(),
						];
					}

					break;

				case QuestObjective::DELIVER:
					$output['amount'] = $entity->getAmount();

					if (
						$entity->getDeliveryType() === DeliveryType::ENDEMIC_LIFE &&
						$projection->isAllowed('endemicLife')
					) {
						$endemicLife = $entity->getEndemicLife();

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

						if ($projection->isAllowed('endemicLife.locations')) {
							$output['endemicLife']['locations'] = $endemicLife->getLocations()->map(
								function(Location $location) use ($projection) {
									$output = [
										'id' => $location->getId(),
									];

									if ($projection->isAllowed('endemicLife.locations.name')) {
										/** @var LocationStrings $strings */
										$strings = $this->getStrings($location);

										$output['name'] = $strings->getName();
									}

									return $output;
								}
							)->toArray();
						}
					} else if (
						$entity->getDeliveryType() === DeliveryType::OBJECT &&
						$projection->isAllowed('objectName')
					) {
						/** @var QuestStrings $strings */
						$strings = $this->getStrings($entity);

						$output['objectName'] = $strings->getObjectName();
					}

					break;

				case QuestObjective::HUNT:
				case QuestObjective::CAPTURE:
				case QuestObjective::SLAY:
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

								$output += [
									'name' => $strings->getName(),
									'description' => $strings->getDescription(),
								];
							}

							return $output;
						}
					)->toArray();
			}

			return $output;
		}
	}