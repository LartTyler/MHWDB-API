<?php
	namespace App\Controller;

	use App\Entity\Ailment;
	use App\Entity\Item;
	use App\Entity\Location;
	use App\Entity\Monster;
	use App\Entity\Skill;
	use App\QueryDocument\Projection;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Routing\RouterInterface;

	class MonsterDataController extends AbstractDataController {
		/**
		 * MonsterDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Monster::class);
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
		 * @param Monster|EntityInterface|null $entity
		 * @param Projection           $projection
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
				'resistances' => $entity->getResistances(),
			];

			if ($projection->isAllowed('ailments')) {
				$output['ailments'] = array_map(function(Ailment $ailment) use ($projection): array {
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
							$output['recovery']['items'] = array_map(function(Item $item): array {
								return [
									'id' => $item->getId(),
									'name' => $item->getName(),
									'description' => $item->getDescription(),
									'rarity' => $item->getRarity(),
									'value' => $item->getValue(),
									'carryLimit' => $item->getCarryLimit(),
								];
							}, $recovery->getItems()->toArray());
						}
					}

					if ($projection->isAllowed('ailments.protection')) {
						$protection = $ailment->getProtection();

						$output['protection'] = [];

						if ($projection->isAllowed('ailments.protection.skills')) {
							$output['protection']['skills'] = array_map(function(Skill $skill): array {
								return [
									'id' => $skill->getId(),
									'name' => $skill->getName(),
									'description' => $skill->getDescription(),
								];
							}, $protection->getSkills()->toArray());
						}

						if ($projection->isAllowed('ailments.protection.items')) {
							$output['protection']['items'] = array_map(function(Item $item): array {
								return [
									'id' => $item->getId(),
									'name' => $item->getName(),
									'description' => $item->getDescription(),
									'rarity' => $item->getRarity(),
									'value' => $item->getValue(),
									'carryLimit' => $item->getCarryLimit(),
								];
							}, $protection->getItems()->toArray());
						}

						if (!$output['protection'])
							unset($output['protection']);
					}

					return $output;
				}, $entity->getAilments()->toArray());
			}

			if ($projection->isAllowed('locations')) {
				$output['locations'] = array_map(function(Location $location): array {
					return [
						'id' => $location->getId(),
						'name' => $location->getName(),
						'zoneCount' => $location->getZoneCount(),
					];
				}, $entity->getLocations()->toArray());
			}

			return $output;
		}
	}