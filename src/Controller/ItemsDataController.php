<?php
	namespace App\Controller;

	use App\Entity\Item;
	use App\QueryDocument\Projection;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class ItemsDataController extends AbstractDataController {
		/**
		 * ItemsDataController constructor.
		 */
		public function __construct() {
			parent::__construct(Item::class);
		}

		/**
		 * @Route(path="/items", methods={"GET"}, name="items.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/items/{item<\d+>}", methods={"GET"}, name="items.read")
		 *
		 * @param Item $item
		 *
		 * @return Response
		 */
		public function read(Item $item): Response {
			return $this->respond($item);
		}

		/**
		 * @param EntityInterface|Item|null $entity
		 * @param Projection                $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			return [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'description' => $entity->getDescription(),
				'rarity' => $entity->getRarity(),
				'carryLimit' => $entity->getCarryLimit(),
				'value' => $entity->getValue(),
			];
		}
	}