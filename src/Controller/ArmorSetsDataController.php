<?php
	namespace App\Controller;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Utility\EntityUtil;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
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
			/** @var ArmorSet[]|Response $items */
			$items = $this->doListAction($request);

			if ($items instanceof Response)
				return $items;

			return $this->respond($this->normalizeManyArmorSets($items));
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return Response
		 */
		public function readAction(string $idOrSlug): Response {
			/** @var ArmorSet|null $armorSet */
			$armorSet = $this->doReadAction($idOrSlug);

			if ($armorSet instanceof ApiErrorInterface)
				return $this->respond($armorSet);

			return $this->respond($this->normalizeOneArmorSet($armorSet));
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

			return EntityUtil::normalize($armorSet);
		}
	}