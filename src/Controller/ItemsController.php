<?php
	namespace App\Controller;

	use App\Contrib\Transformers\ItemTransformer;
	use App\Entity\Item;
	use App\QueryDocument\Projection;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class ItemsController extends AbstractController {
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
		 * @Route(path="/items", methods={"PUT"}, name="items.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ItemTransformer $transformer
		 * @param Request         $request
		 *
		 * @return Response
		 */
		public function create(ItemTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
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
		 * @Route(path="/items/{item<\d+>}", methods={"PATCH"}, name="items.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ItemTransformer $transformer
		 * @param Request         $request
		 * @param Item            $item
		 *
		 * @return Response
		 */
		public function update(ItemTransformer $transformer, Request $request, Item $item): Response {
			return $this->doUpdate($transformer, $item, $request);
		}

		/**
		 * @Route(path="/items/{item<\d+>}", methods={"DELETE"}, name="items.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ItemTransformer $transformer
		 * @param Item            $item
		 *
		 * @return Response
		 */
		public function delete(ItemTransformer $transformer, Item $item): Response {
			return $this->doDelete($transformer, $item);
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