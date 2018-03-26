<?php
	namespace App\Controller;

	use App\Entity\CraftingMaterialCost;
	use App\Entity\Weapon;
	use App\Entity\WeaponCraftingInfo;
	use App\Entity\WeaponUpgradeNode;
	use DaybreakStudios\DozeBundle\ResponderService;
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
			if ($request->query->has('q')) {
				/** @var Response|Weapon[] $results */
				$results = $this->getSearchResults($request->query->all());

				if ($results instanceof Response)
					return $results;

				return $this->respond($this->normalizeWeapons($results));
			}

			/** @var Weapon[] $items */
			$items = $this->manager->getRepository($this->entityClass)->findAll();

			return $this->responder->createResponse($this->normalizeWeapons($items));
		}

		/**
		 * @param string $id
		 *
		 * @return Response
		 */
		public function readAction(string $id): Response {
			/** @var Weapon|null $weapon */
			$weapon = $this->getEntity($id);

			return $this->respond($this->normalizeWeapon($weapon));
		}

		/**
		 * @param Weapon[] $weapons
		 *
		 * @return array
		 */
		protected function normalizeWeapons(array $weapons): array {
			$normalizer = (function(Weapon $weapon): array {
				return $this->normalizeWeapon($weapon);
			})->bindTo($this);

			return array_map(function(Weapon $weapon) use ($normalizer): array {
				return call_user_func($normalizer, $weapon);
			}, $weapons);
		}

		/**
		 * @param Weapon|null $weapon
		 *
		 * @return array|null
		 */
		protected function normalizeWeapon(?Weapon $weapon): ?array {
			if (!$weapon)
				return null;

			return [
				'id' => $weapon->getId(),
				'slug' => $weapon->getSlug(),
				'name' => $weapon->getName(),
				'type' => $weapon->getType(),
				'rarity' => $weapon->getRarity(),
				'attributes' => $weapon->getAttributes(),
				'crafting' => $this->normalizeWeaponCraftingInfo($weapon->getCrafting()),
			];
		}

		/**
		 * @param WeaponCraftingInfo|null $info
		 *
		 * @return array|null
		 */
		protected function normalizeWeaponCraftingInfo(?WeaponCraftingInfo $info): ?array {
			if (!$info)
				return null;

			return [
				'craftable' => $info->isCraftable(),
				'previous' => $info->getPrevious() ? $info->getPrevious()->getId() : null,
				'branches' => array_map(function(Weapon $branch) {
					return $branch->getId();
				}, $info->getBranches()->toArray()),
				'craftingMaterials' => $this->normalizeCraftingMaterialCosts($info->getCraftingMaterials()->toArray()),
				'upgradeMaterials' => $this->normalizeCraftingMaterialCosts($info->getUpgradeMaterials()->toArray()),
			];
		}

		/**
		 * @param CraftingMaterialCost[] $costs
		 *
		 * @return array
		 */
		protected function normalizeCraftingMaterialCosts(array $costs): array {
			$noramlizer = (function(CraftingMaterialCost $cost): array {
				return $this->normalizeCraftingMaterialCost($cost);
			})->bindTo($this);

			return array_map(function(CraftingMaterialCost $cost) use ($noramlizer): array {
				return call_user_func($noramlizer, $cost);
			}, $costs);
		}

		/**
		 * @param CraftingMaterialCost $cost
		 *
		 * @return array
		 */
		protected function normalizeCraftingMaterialCost(CraftingMaterialCost $cost): array {
			$item = $cost->getItem();

			return [
				'quantity' => $cost->getQuantity(),
				'item' => [
					'id' => $item->getId(),
					'name' => $item->getName(),
					'description' => $item->getDescription(),
					'rarity' => $item->getRarity(),
					'sellPrice' => $item->getSellPrice(),
					'buyPrice' => $item->getBuyPrice(),
					'carryLimit' => $item->getCarryLimit(),
				],
			];
		}
	}