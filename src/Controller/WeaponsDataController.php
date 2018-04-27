<?php
	namespace App\Controller;

	use App\Entity\CraftingMaterialCost;
	use App\Entity\Slot;
	use App\Entity\Weapon;
	use App\Entity\WeaponElement;
	use App\Game\WeaponType;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Symfony\Bridge\Doctrine\RegistryInterface;
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
		 * @param EntityInterface|Weapon|null $weapon
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $weapon): ?array {
			if (!$weapon)
				return null;

			/**
			 * @param Collection|CraftingMaterialCost[] $costs
			 *
			 * @return array
			 */
			$materialTransformer = function(Collection $costs): array {
				return array_map(function(CraftingMaterialCost $cost): array {
					$item = $cost->getItem();

					return [
						'quantity' => $cost->getQuantity(),
						'item' => [
							'id' => $item->getId(),
							'name' => $item->getName(),
							'description' => $item->getDescription(),
							'rarity' => $item->getRarity(),
							'carryLimit' => $item->getCarryLimit(),
							'sellPrice' => $item->getSellPrice(),
							'buyPrice' => $item->getBuyPrice(),
						],
					];
				}, $costs->toArray());
			};

			$crafting = $weapon->getCrafting();

			$data = [
				'id' => $weapon->getId(),
				'slug' => $weapon->getSlug(),
				'name' => $weapon->getName(),
				'type' => $weapon->getType(),
				'rarity' => $weapon->getRarity(),
				'slots' => array_map(function(Slot $slot): array {
					return [
						'rank' => $slot->getRank(),
					];
				}, $weapon->getSlots()->toArray()),
				'elements' => array_map(function(WeaponElement $element): array {
					return [
						'type' => $element->getType(),
						'damage' => $element->getDamage(),
						'hidden' => $element->isHidden(),
					];
				}, $weapon->getElements()->toArray()),
				'attributes' => $weapon->getAttributes(),
				'crafting' => $crafting ? [
					'craftable' => $crafting->isCraftable(),
					'previous' => $crafting->getPrevious() ? $crafting->getPrevious()->getId() : null,
					'branches' => array_map(function(Weapon $branch): int {
						return $branch->getId();
					}, $crafting->getBranches()->toArray()),
					'craftingMaterials' => call_user_func($materialTransformer, $crafting->getCraftingMaterials()),
					'upgradeMaterials' => call_user_func($materialTransformer, $crafting->getUpgradeMaterials()),
				] : null,
			];

			if (WeaponType::isMelee($weapon->getType())) {
				$sharpness = $weapon->getSharpness();

				$data += [
					'sharpness' => [
						'red' => $sharpness->getRed(),
						'orange' => $sharpness->getOrange(),
						'yellow' => $sharpness->getYellow(),
						'green' => $sharpness->getGreen(),
						'blue' => $sharpness->getBlue(),
					],
				];
			}

			return $data;
		}
	}