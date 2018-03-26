<?php
	namespace App\Controller;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use DaybreakStudios\DozeBundle\ResponderService;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\RouterInterface;

	class ArmorSetsDataController extends AbstractDataController {
		/**
		 * ArmorSetsDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, ArmorSet::class);
		}

		/**
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function listAction(Request $request): Response {
			if ($request->query->has('q')) {
				$results = $this->getSearchResults($request->query->all());

				if ($results instanceof Response)
					return $results;

				return $this->respond($this->normalizeManyArmorSets($results));
			}

			$items = $this->manager->getRepository($this->entityClass)->findAll();

			return $this->responder->createResponse($this->normalizeManyArmorSets($items));
		}

		/**
		 * @param string $id
		 *
		 * @return Response
		 */
		public function readAction(string $id): Response {
			/** @var ArmorSet|null $armor */
			$armor = $this->getEntity($id);

			return $this->respond($this->normalizeOneArmorSet($armor));
		}

		/**
		 * @param array $items
		 *
		 * @return array
		 */
		protected function normalizeManyArmorSets(array $items): array {
			return array_map((function(ArmorSet $armorSet): array {
				return $this->normalizeOneArmorSet($armorSet);
			})->bindTo($this), $items);
		}

		/**
		 * @param ArmorSet|null $armorSet
		 *
		 * @return array|null
		 */
		protected function normalizeOneArmorSet(?ArmorSet $armorSet): ?array {
			if (!$armorSet)
				return null;

			return [
				'id' => $armorSet->getId(),
				'name' => $armorSet->getName(),
				'rank' => $armorSet->getRank(),
				'pieces' => array_map(function(Armor $armor): array {
					return $armor->getId();
				}, $armorSet->getPieces()->toArray()),
			];
		}
	}