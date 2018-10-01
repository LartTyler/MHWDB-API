<?php
	namespace App\Controller;

	use App\Contrib\Transformers\MonsterTransformer;
	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\MonsterResistance;
	use App\Entity\MonsterWeakness;
	use App\Entity\Skill;
	use App\QueryDocument\Projection;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class MonsterDataController extends AbstractDataController {
		/**
		 * MonsterDataController constructor.
		 */
		public function __construct() {
			parent::__construct(Monster::class);
		}

		/**
		 * @Route(path="/monsters", methods={"GET"}, name="monsters.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
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
		 * @param Monster $monster
		 *
		 * @return Response
		 */
		public function read(Monster $monster): Response {
			return $this->respond($monster);
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
		 * @param Monster|EntityInterface|null $entity
		 * @param Projection                   $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'type' => $entity->getType(),
				'species' => $entity->getSpecies(),
				'description' => $entity->getDescription(),
				'elements' => $entity->getElements(),
			];

			if ($projection->isAllowed('ailments')) {
				$output['ailments'] = array_map(
					function(Ailment $ailment) use ($projection): array {
						$output = [
							'name' => $ailment->getName(),
							'description' => $ailment->getDescription(),
						];

						if ($projection->isAllowed('ailments.recovery')) {
							$recovery = $ailment->getRecovery();

							$output['recovery'] = [
								'actions' => $recovery->getActions(),
							];

							if ($projection->isAllowed('ailments.recovery.items')) {
								$output['recovery']['items'] = array_map(
									function(Item $item): array {
										return [
											'id' => $item->getId(),
											'name' => $item->getName(),
											'description' => $item->getDescription(),
											'rarity' => $item->getRarity(),
											'value' => $item->getValue(),
											'carryLimit' => $item->getCarryLimit(),
										];
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
									function(Item $item): array {
										return [
											'id' => $item->getId(),
											'name' => $item->getName(),
											'description' => $item->getDescription(),
											'rarity' => $item->getRarity(),
											'value' => $item->getValue(),
											'carryLimit' => $item->getCarryLimit(),
										];
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
					function(Location $location): array {
						return [
							'id' => $location->getId(),
							'name' => $location->getName(),
							'zoneCount' => $location->getZoneCount(),
						];
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
					function(MonsterWeakness $weakness): array {
						return [
							'element' => $weakness->getElement(),
							'stars' => $weakness->getStars(),
							'condition' => $weakness->getCondition(),
						];
					},
					$entity->getWeaknesses()->toArray()
				);
			}

			return $output;
		}
	}