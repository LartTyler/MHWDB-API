<?php
	namespace App\Controller;

	use App\Entity\MonsterReward;
	use App\Entity\RewardCondition;
	use App\QueryDocument\Projection;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class MonsterRewardsController extends AbstractController {
		/**
		 * MonsterRewardsController constructor.
		 */
		public function __construct() {
			parent::__construct(MonsterReward::class);
		}

		/**
		 * @Route(path="/monsters/rewards", methods={"GET"}, name="monsters.rewards.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/monsters/rewards/{reward<\d+>}", methods={"GET"}, name="monsters.rewards.read")
		 *
		 * @param MonsterReward $reward
		 *
		 * @return Response
		 */
		public function read(MonsterReward $reward): Response {
			return $this->respond($reward);
		}

		/**
		 * @param EntityInterface|MonsterReward|null $entity
		 * @param Projection                         $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
			];

			if ($projection->isAllowed('conditions')) {
				$output['conditions'] = $entity->getConditions()->map(
					function(RewardCondition $condition): array {
						return [
							'type' => $condition->getType(),
							'subtype' => $condition->getSubtype(),
							'rank' => $condition->getRank(),
							'stackSize' => $condition->getStackSize(),
							'chance' => $condition->getChance(),
						];
					}
				);
			}

			if ($projection->isAllowed('monster')) {
				$monster = $entity->getMonster();

				$output['monster'] = [
					'id' => $monster->getId(),
					'name' => $monster->getName(),
					'type' => $monster->getType(),
					'species' => $monster->getSpecies(),
				];
			}

			if ($projection->isAllowed('item')) {
				$item = $entity->getItem();

				$output['item'] = [
					'id' => $item->getId(),
					'name' => $item->getName(),
					'description' => $item->getDescription(),
					'rarity' => $item->getRarity(),
					'carryLimit' => $item->getCarryLimit(),
					'value' => $item->getValue(),
				];
			}

			return $output;
		}
	}