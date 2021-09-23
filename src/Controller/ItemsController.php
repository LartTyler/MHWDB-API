<?php
	namespace App\Controller;

	use App\Contrib\Transformers\ItemTransformer;
	use App\Entity\Item;
	use App\Entity\Strings\ItemStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class ItemsController extends AbstractController {
		/**
		 * ItemsDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Item::class);
		}

		/**
		 * @Route(path="/items", methods={"GET"}, name="items.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
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
		 * @param Request $request
		 * @param Item    $item
		 *
		 * @return Response
		 */
		public function read(Request $request, Item $item): Response {
			return $this->respond($request, $item);
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
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Item);

			$output = [
				'id' => $entity->getId(),
				'rarity' => $entity->getRarity(),
				'carryLimit' => $entity->getCarryLimit(),
				'value' => $entity->getValue(),
				'buyPrice' => $entity->getBuyPrice(),
				'sellPrice' => $entity->getSellPrice(),
			];

			if ($projection->isAllowed('name') || $projection->isAllowed('description')) {
				/** @var ItemStrings $strings */
				$strings = $this->getStrings($entity);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			return $output;
		}
	}