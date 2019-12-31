<?php
	namespace App\Controller;

	use App\Contrib\Transformers\MonsterTransformer;
	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MonsterResistance;
	use App\Entity\MonsterReward;
	use App\Entity\MonsterWeakness;
	use App\Entity\RewardCondition;
	use App\Entity\Skill;
	use App\Entity\Strings\AilmentStrings;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\Strings\LocationStrings;
	use App\Entity\Strings\MonsterStrings;
	use App\Entity\Strings\MonsterWeaknessStrings;
	use App\Entity\Strings\RewardConditionStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class MonsterController extends AbstractController {
		/**
		 * MonsterDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Monster::class);
		}

		/**
		 * @Route(path="/monsters", methods={"GET"}, name="monsters.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/monsters", methods={"PUT"}, name="monsters.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param MonsterTransformer $transformer
		 * @param Request            $request
		 *
		 * @return Response
		 */
		public function create(MonsterTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/monsters/{monster<\d+>}", methods={"GET"}, name="monsters.read")
		 *
		 * @param Request $request
		 * @param Monster $monster
		 *
		 * @return Response
		 */
		public function read(Request $request, Monster $monster): Response {
			return $this->respond($request, $monster);
		}

		/**
		 * @Route(path="/monsters/{monster<\d+>}", methods={"PATCH"}, name="monsters.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param MonsterTransformer $transformer
		 * @param Request            $request
		 * @param Monster            $monster
		 *
		 * @return Response
		 */
		public function update(MonsterTransformer $transformer, Request $request, Monster $monster): Response {
			return $this->doUpdate($transformer, $monster, $request);
		}

		/**
		 * @Route(path="/monsters/{monster<\d+>}", methods={"DELETE"}, name="monsters.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param MonsterTransformer $transformer
		 * @param Monster            $monster
		 *
		 * @return Response
		 */
		public function delete(MonsterTransformer $transformer, Monster $monster): Response {
			return $this->doDelete($transformer, $monster);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Monster);

			$output = [
				'id' => $entity->getId(),
				'type' => $entity->getType(),
				'species' => $entity->getSpecies(),
				'elements' => $entity->getElements(),
			];

			if ($projection->isAllowed('name') || $projection->isAllowed('description')) {
				/** @var MonsterStrings $strings */
				$strings = $this->getStrings($entity);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			if ($projection->isAllowed('ailments')) {
				$output['ailments'] = array_map(
					function(Ailment $ailment) use ($projection): array {
						$output = [
							'id' => $ailment->getId(),
						];

						if ($projection->isAllowed('ailments.name') || $projection->isAllowed('ailments.description')) {
							/** @var AilmentStrings $strings */
							$strings = $this->getStrings($ailment);

							$output += [
								'name' => $strings->getName(),
								'description' => $strings->getDescription(),
							];
						}

						if ($projection->isAllowed('ailments.recovery')) {
							$recovery = $ailment->getRecovery();

							$output['recovery'] = [
								'actions' => $recovery->getActions(),
							];

							if ($projection->isAllowed('ailments.recovery.items')) {
								$output['recovery']['items'] = array_map(
									function(Item $item) use ($projection): array {
										$output = [
											'id' => $item->getId(),
											'rarity' => $item->getRarity(),
											'value' => $item->getValue(),
											'carryLimit' => $item->getCarryLimit(),
										];

										if (
											$projection->isAllowed('ailments.recovery.items.name') ||
											$projection->isAllowed('ailments.recovery.items.description')
										) {
											/** @var ItemStrings $strings */
											$strings = $this->getStrings($item);

											$output += [
												'name' => $strings->getName(),
												'description' => $strings->getDescription(),
											];
										}

										return $output;
									},
									$recovery->getItems()->toArray()
								);
							}
						}

						if ($projection->isAllowed('ailments.protection')) {
							$protection = $ailment->getProtection();

							$output['protection'] = [];

							if ($projection->isAllowed('ailments.protection.skills')) {
								$output['protection']['skills'] = array_map(
									function(Skill $skill): array {
										return [
											'id' => $skill->getId(),
											'name' => $skill->getName(),
											'description' => $skill->getDescription(),
										];
									},
									$protection->getSkills()->toArray()
								);
							}

							if ($projection->isAllowed('ailments.protection.items')) {
								$output['protection']['items'] = array_map(
									function(Item $item) use ($projection): array {
										$output = [
											'id' => $item->getId(),
											'rarity' => $item->getRarity(),
											'value' => $item->getValue(),
											'carryLimit' => $item->getCarryLimit(),
										];

										if (
											$projection->isAllowed('ailments.protection.items.name') ||
											$projection->isAllowed('ailments.protection.items.description')
										) {
											/** @var ItemStrings $strings */
											$strings = $this->getStrings($item);

											$output += [
												'name' => $strings->getName(),
												'description' => $strings->getDescription(),
											];
										}

										return $output;
									},
									$protection->getItems()->toArray()
								);
							}

							if (!$output['protection'])
								unset($output['protection']);
						}

						return $output;
					},
					$entity->getAilments()->toArray()
				);
			}

			if ($projection->isAllowed('locations')) {
				$output['locations'] = array_map(
					function(Location $location) use ($projection): array {
						$output = [
							'id' => $location->getId(),
							'zoneCount' => $location->getZoneCount(),
						];

						if ($projection->isAllowed('locations.name')) {
							/** @var LocationStrings $strings */
							$strings = $this->getStrings($location);

							$output['name'] = $strings->getName();
						}

						return $output;
					},
					$entity->getLocations()->toArray()
				);
			}

			if ($projection->isAllowed('resistances')) {
				$output['resistances'] = array_map(
					function(MonsterResistance $resistance): array {
						return [
							'element' => $resistance->getElement(),
							'condition' => $resistance->getCondition(),
						];
					},
					$entity->getResistances()->toArray()
				);
			}

			if ($projection->isAllowed('weaknesses')) {
				$output['weaknesses'] = array_map(
					function(MonsterWeakness $weakness) use ($projection): array {
						$output = [
							'element' => $weakness->getElement(),
							'stars' => $weakness->getStars(),
						];

						if ($projection->isAllowed('weaknesses.condition')) {
							/** @var MonsterWeaknessStrings $strings */
							$strings = $this->getStrings($weakness);

							$output['condition'] = $strings->getCondition();
						}

						return $output;
					},
					$entity->getWeaknesses()->toArray()
				);
			}

			if ($projection->isAllowed('rewards')) {
				$output['rewards'] = $entity->getRewards()->map(
					function(MonsterReward $reward) use ($projection): array {
						$output = [
							'id' => $reward->getId(),
						];

						if ($projection->isAllowed('rewards.item')) {
							$item = $reward->getItem();

							$output['item'] = [
								'id' => $item->getId(),
								'rarity' => $item->getRarity(),
								'carryLimit' => $item->getCarryLimit(),
								'value' => $item->getValue(),
							];

							if (
								$projection->isAllowed('rewards.item.name') ||
								$projection->isAllowed('rewards.item.description')
							) {
								/** @var ItemStrings $strings */
								$strings = $this->getStrings($item);

								$output['item'] += [
									'name' => $strings->getName(),
									'description' => $strings->getDescription(),
								];
							}
						}

						if ($projection->isAllowed('rewards.condition')) {
							$output['conditions'] = $reward->getConditions()->map(
								function(RewardCondition $condition) use ($projection): array {
									$output = [
										'type' => $condition->getType(),
										'rank' => $condition->getRank(),
										'quantity' => $condition->getQuantity(),
										'chance' => $condition->getChance(),
									];

									if ($projection->isAllowed('rewards.condition.subtype')) {
										/** @var RewardConditionStrings $strings */
										$strings = $this->getStrings($condition);

										$output['subtype'] = $strings->getSubtype();
									}

									return $output;
								}
							)->toArray();
						}

						return $output;
					}
				)->toArray();
			}

			return $output;
		}
	}