<?php
	namespace App\Controller;

	use App\Contrib\Transformers\LocationTransformer;
	use App\Entity\Camp;
	use App\Entity\Location;
	use App\QueryDocument\Projection;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class LocationDataController extends AbstractDataController {
		/**
		 * LocationDataController constructor.
		 */
		public function __construct() {
			parent::__construct(Location::class);
		}

		/**
		 * @Route(path="/locations", methods={"GET"}, name="locations.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
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
		 * @param Location $location
		 *
		 * @return Response
		 */
		public function read(Location $location): Response {
			return $this->respond($location);
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
		 * @param Location|EntityInterface|null $entity
		 * @param Projection                    $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'zoneCount' => $entity->getZoneCount(),
			];

			if ($projection->isAllowed('camps')) {
				$output['camps'] = array_map(function(Camp $camp): array {
					return [
						'id' => $camp->getId(),
						'name' => $camp->getName(),
						'zone' => $camp->getZone(),
					];
				}, $entity->getCamps()->toArray());
			}

			return $output;
		}
	}