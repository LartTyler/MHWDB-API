<?php
	namespace App\Controller;

	use App\Entity\MonsterReward;
	use App\Entity\RewardCondition;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\Strings\MonsterStrings;
	use App\Entity\Strings\RewardConditionStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class MonsterRewardsController extends AbstractController {
		/**
		 * MonsterRewardsController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, MonsterReward::class);
		}

		/**
		 * @Route(path="/monsters/rewards", methods={"GET"}, name="monsters.rewards.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/monsters/rewards/{reward<\d+>}", methods={"GET"}, name="monsters.rewards.read")
		 *
		 * @param Request       $request
		 * @param MonsterReward $reward
		 *
		 * @return Response
		 */
		public function read(Request $request, MonsterReward $reward): Response {
			return $this->respond($request, $reward);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof MonsterReward);

			$output = [
				'id' => $entity->getId(),
			];

			if ($projection->isAllowed('conditions')) {
				$output['conditions'] = $entity->getConditions()->map(
					function(RewardCondition $condition) use ($projection): array {
						$output = [
							'type' => $condition->getType(),
							'rank' => $condition->getRank(),
							'quantity' => $condition->getQuantity(),
							'chance' => $condition->getChance(),
						];

						if ($projection->isAllowed('conditions.subtype')) {
							/** @var RewardConditionStrings $strings */
							$strings = $this->getStrings($condition);

							$output['subtype'] = $strings->getSubtype();
						}

						return $output;
					}
				);
			}

			if ($projection->isAllowed('monster')) {
				$monster = $entity->getMonster();

				$output['monster'] = [
					'id' => $monster->getId(),
					'type' => $monster->getType(),
					'species' => $monster->getSpecies(),
				];

				if ($projection->isAllowed('monster.name')) {
					/** @var MonsterStrings $strings */
					$strings = $this->getStrings($monster);

					$output['name'] = $strings->getName();
				}
			}

			if ($projection->isAllowed('item')) {
				$item = $entity->getItem();

				$output['item'] = [
					'id' => $item->getId(),
					'rarity' => $item->getRarity(),
					'carryLimit' => $item->getCarryLimit(),
					'value' => $item->getValue(),
				];

				if ($projection->isAllowed('item.name') || $projection->isAllowed('item.description')) {
					/** @var ItemStrings $strings */
					$strings = $this->getStrings($item);

					$output['item'] += [
						'name' => $strings->getName(),
						'description' => $strings->getDescription(),
					];
				}
			}

			return $output;
		}
	}