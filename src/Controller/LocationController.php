<?php
	namespace App\Controller;

	use App\Contrib\Transformers\LocationTransformer;
	use App\Entity\Camp;
	use App\Entity\Location;
	use App\Entity\Strings\CampStrings;
	use App\Entity\Strings\LocationStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class LocationController extends AbstractController {
		/**
		 * LocationDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Location::class);
		}

		/**
		 * @Route(path="/locations", methods={"GET"}, name="locations.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList(
				$request,
				[
					'strings.language' => $request->getLocale(),
					'camp.strings.language' => $request->getLocale(),
				]
			);
		}

		/**
		 * @Route(path="/locations", methods={"PUT"}, name="locations.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param LocationTransformer $transformer
		 * @param Request             $request
		 *
		 * @return Response
		 */
		public function create(LocationTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/locations/{location<\d+>}", methods={"GET"}, name="locations.read")
		 *
		 * @param Request  $request
		 * @param Location $location
		 *
		 * @return Response
		 */
		public function read(Request $request, Location $location): Response {
			return $this->respond($request, $location);
		}

		/**
		 * @Route(path="/locations/{location<\d+>}", methods={"PATCH"}, name="locations.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param LocationTransformer $transformer
		 * @param Request             $request
		 * @param Location            $location
		 *
		 * @return Response
		 */
		public function update(LocationTransformer $transformer, Request $request, Location $location): Response {
			return $this->doUpdate($transformer, $location, $request);
		}

		/**
		 * @Route(path="/locations/{location<\d+>}", methods={"DELETE"}, name="locations.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param LocationTransformer $transformer
		 * @param Location            $location
		 *
		 * @return Response
		 */
		public function delete(LocationTransformer $transformer, Location $location): Response {
			return $this->doDelete($transformer, $location);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Location);

			$output = [
				'id' => $entity->getId(),
				'zoneCount' => $entity->getZoneCount(),
			];

			if ($projection->isAllowed('name')) {
				/** @var LocationStrings $strings */
				$strings = $this->getStrings($entity);

				$output['name'] = $strings->getName();
			}

			if ($projection->isAllowed('camps')) {
				$output['camps'] = array_map(
					function(Camp $camp) use ($projection): array {
						$output = [
							'id' => $camp->getId(),
							'zone' => $camp->getZone(),
						];

						if ($projection->isAllowed('camps.name')) {
							/** @var CampStrings $strings */
							$strings = $this->getStrings($camp);

							$output['name'] = $strings->getName();
						}

						return $output;
					},
					$entity->getCamps()->toArray()
				);
			}

			return $output;
		}
	}