<?php
	namespace App\Controller;

	use App\Entity\Weapon;
	use App\Utility\EntityUtil;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\RouterInterface;

	class WeaponsDataController extends AbstractDataController {
		/**
		 * WeaponsDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Weapon::class);
		}

		/**
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function listAction(Request $request): Response {
			/** @var Weapon[]|Response $items */
			$items = $this->doListAction($request);

			if ($items instanceof Response)
				return $items;

			return $this->respond($this->normalizeManyWeapons($items));
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return Response
		 */
		public function readAction(string $idOrSlug): Response {
			/** @var Weapon|ApiErrorInterface|null $weapon */
			$weapon = $this->doReadAction($idOrSlug);

			if ($weapon instanceof ApiErrorInterface)
				return $this->respond($weapon);

			return $this->respond($this->normalizeOneWeapon($weapon));
		}

		/**
		 * @param Weapon[] $weapons
		 *
		 * @return array
		 */
		protected function normalizeManyWeapons(array $weapons): array {
			return array_map((function(Weapon $weapon): array {
				return $this->normalizeOneWeapon($weapon);
			})->bindTo($this), $weapons);
		}

		/**
		 * @param Weapon|null $weapon
		 *
		 * @return array|null
		 */
		protected function normalizeOneWeapon(?Weapon $weapon): ?array {
			if (!$weapon)
				return null;

			$toIdTransformer = function(?EntityInterface $entity): ?int {
				return $entity ? $entity->getId() : null;
			};

			$materialCostKeys = [
				'quantity',
				'item' => [
					'id',
					'name',
					'description',
					'rarity',
					'sellPrice',
					'buyPrice',
					'carryLimit',
				],
			];

			return EntityUtil::normalize($weapon, [
				'id',
				'slug',
				'name',
				'type',
				'rarity',
				'attributes',
				'crafting' => [
					'craftable',
					'previous' => $toIdTransformer,
					'branches' => $toIdTransformer,
					'craftingMaterials' => $materialCostKeys,
					'upgradeMaterials' => $materialCostKeys,
				],
			]);
		}
	}