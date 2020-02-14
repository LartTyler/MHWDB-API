<?php
	namespace App\Controller;

	use App\Contrib\Transformers\EndemicLifeTransformer;
	use App\Entity\Camp;
	use App\Entity\EndemicLife;
	use App\Entity\Location;
	use App\Entity\Strings\CampStrings;
	use App\Entity\Strings\EndemicLifeStrings;
	use App\Entity\Strings\LocationStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class EndemicLifeController extends AbstractController {
		/**
		 * EndemicLifeController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, EndemicLife::class);
		}

		/**
		 * @Route(path="/endemic-life", methods={"GET"}, name="endemic-life.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/endemic-life", methods={"PUT"}, name="endemic-life.create")
		 *
		 * @param Request                $request
		 * @param EndemicLifeTransformer $transformer
		 *
		 * @return Response
		 */
		public function create(Request $request, EndemicLifeTransformer $transformer): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/endemic-life/{endemicLife<\d+>}", methods={"GET"}, name="endemic-life.read")
		 *
		 * @param Request     $request
		 * @param EndemicLife $endemicLife
		 *
		 * @return Response
		 */
		public function read(Request $request, EndemicLife $endemicLife): Response {
			return $this->respond($request, $endemicLife);
		}

		/**
		 * @Route(path="/endemic-life/{endemicLife<\d+>}", methods={"PATCH"}, name="endemic-life.update")
		 *
		 * @param Request                $request
		 * @param EndemicLife            $endemicLife
		 * @param EndemicLifeTransformer $transformer
		 *
		 * @return Response
		 */
		public function update(
			Request $request,
			EndemicLife $endemicLife,
			EndemicLifeTransformer $transformer
		): Response {
			return $this->doUpdate($transformer, $endemicLife, $request);
		}

		/**
		 * @Route(path="/endemic-life/{endemicLife<\d+>}", methods={"DELETE"}, name="endemic-life.delete")
		 *
		 * @param EndemicLife            $endemicLife
		 * @param EndemicLifeTransformer $transformer
		 *
		 * @return Response
		 */
		public function delete(EndemicLife $endemicLife, EndemicLifeTransformer $transformer): Response {
			return $this->doDelete($transformer, $endemicLife);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof EndemicLife);

			$output = [
				'id' => $entity->getId(),
				'type' => $entity->getType(),
				'researchPointValue' => $entity->getResearchPointValue(),
				'spawnConditions' => $entity->getSpawnConditions(),
			];

			if ($projection->isAllowed('name') || $projection->isAllowed('description')) {
				/** @var EndemicLifeStrings $strings */
				$strings = $this->getStrings($entity);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			if ($projection->isAllowed('locations')) {
				$output['locations'] = $entity->getLocations()->map(
					function(Location $location) use ($projection) {
						$output = [
							'id' => $location->getId(),
							'zoneCount' => $location->getZoneCount(),
						];

						if ($projection->isAllowed('locations.name')) {
							/** @var LocationStrings $strings */
							$strings = $this->getStrings($location);

							$output['name'] = $strings->getName();
						}

						if ($projection->isAllowed('locations.camps')) {
							$output['camps'] = $location->getCamps()->map(
								function(Camp $camp) use ($projection) {
									$output = [
										'id' => $camp->getId(),
										'zone' => $camp->getZone(),
									];

									if ($projection->isAllowed('locations.camps.name')) {
										/** @var CampStrings $strings */
										$strings = $this->getStrings($camp);

										$output['name'] = $strings->getName();
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