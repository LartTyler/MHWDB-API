<?php
	namespace App\Controller;

	use App\Entity\CraftingMaterialCost;
	use App\Entity\Slot;
	use App\Entity\Weapon;
	use App\Entity\WeaponElement;
	use App\Game\WeaponType;
	use App\Response\Projection;
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
		 * @param EntityInterface|Weapon|null $entity
		 * @param Projection                  $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
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

			$crafting = $entity->getCrafting();
			$assets = $entity->getAssets();

			$data = [
				'id' => $entity->getId(),
				'slug' => $entity->getSlug(),
				'name' => $entity->getName(),
				'type' => $entity->getType(),
				'rarity' => $entity->getRarity(),
				'attack' => [
					'display' => $entity->getAttack()->getDisplay(),
					'raw' => $entity->getAttack()->getRaw(),
				],
				'slots' => array_map(function(Slot $slot): array {
					return [
						'rank' => $slot->getRank(),
					];
				}, $entity->getSlots()->toArray()),
				'elements' => array_map(function(WeaponElement $element): array {
					return [
						'type' => $element->getType(),
						'damage' => $element->getDamage(),
						'hidden' => $element->isHidden(),
					];
				}, $entity->getElements()->toArray()),
				// default to \stdClass to fix an empty array being returned instead of an empty object
				'attributes' => $entity->getAttributes() ?: new \stdClass(),
				'crafting' => $crafting ? [
					'craftable' => $crafting->isCraftable(),
					'previous' => $crafting->getPrevious() ? $crafting->getPrevious()->getId() : null,
					'branches' => array_map(function(Weapon $branch): int {
						return $branch->getId();
					}, $crafting->getBranches()->toArray()),
					'craftingMaterials' => call_user_func($materialTransformer, $crafting->getCraftingMaterials()),
					'upgradeMaterials' => call_user_func($materialTransformer, $crafting->getUpgradeMaterials()),
				] : null,
				'assets' => $assets ? [
					'icon' => $assets->getIcon() ? $assets->getIcon()->getUri() : null,
					'image' => $assets->getImage() ? $assets->getImage()->getUri() : null,
				] : null,
			];

			if (WeaponType::isMelee($entity->getType())) {
				$sharpness = $entity->getSharpness();

				$data += [
					'sharpness' => [
						'red' => $sharpness->getRed(),
						'orange' => $sharpness->getOrange(),
						'yellow' => $sharpness->getYellow(),
						'green' => $sharpness->getGreen(),
						'blue' => $sharpness->getBlue(),
						'white' => $sharpness->getWhite(),
					],
				];
			}

			return $data;
		}
	}