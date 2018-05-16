<?php
	namespace App\Controller;

	use App\Entity\CraftingMaterialCost;
	use App\Entity\Slot;
	use App\Entity\Weapon;
	use App\Entity\WeaponElement;
	use App\Game\WeaponType;
	use App\QueryDocument\Projection;
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

			$output = [
				'id' => $entity->getId(),
				'slug' => $entity->getSlug(),
				'name' => $entity->getName(),
				'type' => $entity->getType(),
				'rarity' => $entity->getRarity(),
				'attack' => [
					'display' => $entity->getAttack()->getDisplay(),
					'raw' => $entity->getAttack()->getRaw(),
				],
				// default to \stdClass to fix an empty array being returned instead of an empty object
				'attributes' => $entity->getAttributes() ?: new \stdClass(),
			];

			if (WeaponType::isMelee($entity->getType()) && $projection->isAllowed('sharpness')) {
				$sharpness = $entity->getSharpness();

				$output['sharpness'] = [
					'red' => $sharpness->getRed(),
					'orange' => $sharpness->getOrange(),
					'yellow' => $sharpness->getYellow(),
					'green' => $sharpness->getGreen(),
					'blue' => $sharpness->getBlue(),
					'white' => $sharpness->getWhite(),
				];
			}

			if ($projection->isAllowed('slots')) {
				$output['slots'] = array_map(function(Slot $slot): array {
					return [
						'rank' => $slot->getRank(),
					];
				}, $entity->getSlots()->toArray());
			}

			if ($projection->isAllowed('elements')) {
				$output['elements'] = array_map(function(WeaponElement $element): array {
					return [
						'type' => $element->getType(),
						'damage' => $element->getDamage(),
						'hidden' => $element->isHidden(),
					];
				}, $entity->getElements()->toArray());
			}

			if ($projection->isAllowed('crafting')) {
				$crafting = $entity->getCrafting();

				if ($crafting) {
					/**
					 * @param string                            $type
					 * @param Collection|CraftingMaterialCost[] $costs
					 *
					 * @return array
					 */
					$transformer = function(string $type, Collection $costs) use ($projection): array {
						return array_map(function(CraftingMaterialCost $cost) use ($projection, $type): array {
							$output = [
								'quantity' => $cost->getQuantity(),
							];

							if ($projection->isAllowed(sprintf('crafting.%s.item', $type))) {
								$item = $cost->getItem();

								$output['item'] = [
									'id' => $item->getId(),
									'name' => $item->getName(),
									'description' => $item->getDescription(),
									'rarity' => $item->getRarity(),
									'carryLimit' => $item->getCarryLimit(),
									'sellPrice' => $item->getSellPrice(),
									'buyPrice' => $item->getBuyPrice(),
								];
							}

							return $output;
						}, $costs->toArray());
					};

					$output['crafting'] = [
						'craftable' => $crafting->isCraftable(),
					];

					if ($projection->isAllowed('crafting.previous')) {
						$previous = $crafting->getPrevious();

						$output['crafting']['previous'] = $previous ? $previous->getId() : null;
					}

					if ($projection->isAllowed('crafting.branches')) {						$output['crafting']['branches'] = array_map(function(Weapon $branch): int {
							return $branch->getId();
						}, $crafting->getBranches()->toArray());
					}

					if ($projection->isAllowed('crafting.craftingMaterials')) {
						$output['crafting']['craftingMaterials'] = call_user_func($transformer, 'craftingMaterials',
							$crafting->getCraftingMaterials());
					}

					if ($projection->isAllowed('crafting.upgradeMaterials')) {
						$output['crafting']['upgradeMaterials'] = call_user_func($transformer, 'upgradeMaterials',
							$crafting->getUpgradeMaterials());
					}
				} else
					$output['crafting'] = null;
			}

			if ($projection->isAllowed('assets')) {
				$assets = $entity->getAssets();

				if ($assets) {
					$output['assets'] = [];

					if ($projection->isAllowed('assets.icon')) {
						$icon = $assets->getIcon();

						$output['assets']['icon'] = $icon ? $icon->getUri() : null;
					}

					if ($projection->isAllowed('assets.image')) {
						$image = $assets->getImage();

						$output['assets']['image'] = $image ? $image->getUri() : null;
					}
				} else
					$output['assets'] = null;
			}

			return $output;
		}
	}