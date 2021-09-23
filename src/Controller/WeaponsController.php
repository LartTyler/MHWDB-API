<?php
	namespace App\Controller;

	use App\Contrib\Transformers\WeaponTransformer;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\Strings\WeaponStrings;
	use App\Entity\Weapon;
	use App\Entity\WeaponElement;
	use App\Entity\WeaponSharpness;
	use App\Entity\WeaponSlot;
	use App\Game\WeaponType;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class WeaponsController extends AbstractController {
		/**
		 * WeaponsDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Weapon::class);
		}

		/**
		 * @Route(path="/weapons", methods={"GET"}, name="weapons.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/weapons", methods={"PUT"}, name="weapons.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param WeaponTransformer $transformer
		 * @param Request           $request
		 *
		 * @return Response
		 */
		public function create(WeaponTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/weapons/{weapon<\d+>}", methods={"GET"}, name="weapons.read")
		 *
		 * @param Request $request
		 * @param Weapon  $weapon
		 *
		 * @return Response
		 */
		public function read(Request $request, Weapon $weapon): Response {
			return $this->respond($request, $weapon);
		}

		/**
		 * @Route(path="/weapons/{weapon<\d+>}", methods={"PATCH"}, name="weapons.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param WeaponTransformer $transformer
		 * @param Request           $request
		 * @param Weapon            $weapon
		 *
		 * @return Response
		 */
		public function update(WeaponTransformer $transformer, Request $request, Weapon $weapon): Response {
			return $this->doUpdate($transformer, $weapon, $request);
		}

		/**
		 * @Route(path="/weapons/{weapon<\d+>}", methods={"DELETE"}, name="weapons.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param WeaponTransformer $transformer
		 * @param Weapon            $weapon
		 *
		 * @return Response
		 */
		public function delete(WeaponTransformer $transformer, Weapon $weapon): Response {
			return $this->doDelete($transformer, $weapon);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Weapon);

			$output = [
				'id' => $entity->getId(),
				'type' => $entity->getType(),
				'rarity' => $entity->getRarity(),
				'attack' => [
					'display' => $entity->getAttack()->getDisplay(),
					'raw' => $entity->getAttack()->getRaw(),
				],
				'elderseal' => $entity->getElderseal(),
				// default to \stdClass to fix an empty array being returned instead of an empty object
				'attributes' => $entity->getAttributes() ?: new \stdClass(),
				'damageType' => $entity->getDamageType(),
			];

			if ($projection->isAllowed('name')) {
				/** @var WeaponStrings $strings */
				$strings = $this->getStrings($entity);

				$output['name'] = $strings->getName();
			}

			if ($entity->getType() === WeaponType::GUNLANCE && $projection->isAllowed('shelling')) {
				if ($shelling = $entity->getShelling()) {
					$output['shelling'] = [
						'type' => $shelling->getType(),
						'level' => $shelling->getLevel(),
					];
				} else
					$output['shelling'] = null;
			}

			// region Durability Fields
			if (WeaponType::isMelee($entity->getType()) && $projection->isAllowed('durability')) {
				$output['durability'] = $entity->getDurability()->map(
					function(WeaponSharpness $sharpness) {
						return [
							'red' => $sharpness->getRed(),
							'orange' => $sharpness->getOrange(),
							'yellow' => $sharpness->getYellow(),
							'green' => $sharpness->getGreen(),
							'blue' => $sharpness->getBlue(),
							'white' => $sharpness->getWhite(),
							'purple' => $sharpness->getPurple(),
						];
					}
				)->toArray();
			}
			// endregion

			// region Bowgun Fields
			if (WeaponType::isBowgun($entity->getType())) {
				$output['specialAmmo'] = $entity->getSpecialAmmo();
				$output['deviation'] = $entity->getDeviation();

				if ($projection->isAllowed('ammo')) {
					$normalized = [];

					foreach ($entity->getAmmo() as $ammo) {
						if ($ammo->isEmpty())
							continue;

						$normalized[] = [
							'type' => $ammo->getType(),
							'capacities' => $ammo->getCapacities(),
						];
					}

					$output['ammo'] = $normalized;
				}
			}
			// endregion

			// region Bow Fields
			if ($entity->getType() === WeaponType::BOW)
				$output['coatings'] = $entity->getCoatings();
			// endregion

			// region Insect Glaive Fields
			if ($entity->getType() === WeaponType::INSECT_GLAIVE)
				$output['boostType'] = $entity->getBoostType();
			// endregion

			// region Phial Fields
			if (WeaponType::hasPhialType($entity->getType()) && $projection->isAllowed('phial')) {
				$output['phial'] = $entity->getPhial() ? [
					'type' => $entity->getPhial()->getType(),
					'damage' => $entity->getPhial()->getDamage(),
				] : null;
			}
			//endregion

			// region Slots Fields
			if ($projection->isAllowed('slots')) {
				$output['slots'] = array_map(
					function(WeaponSlot $slot): array {
						return [
							'rank' => $slot->getRank(),
						];
					},
					$entity->getSlots()->toArray()
				);
			}
			// endregion

			// region Elements Fields
			if ($projection->isAllowed('elements')) {
				$output['elements'] = array_map(
					function(WeaponElement $element): array {
						return [
							'type' => $element->getType(),
							'damage' => $element->getDamage(),
							'hidden' => $element->isHidden(),
						];
					},
					$entity->getElements()->toArray()
				);
			}
			// endregion

			// region Crafting Fields
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
						return array_map(
							function(CraftingMaterialCost $cost) use ($projection, $type): array {
								$output = [
									'quantity' => $cost->getQuantity(),
								];

								// region Item Fields
								if ($projection->isAllowed(sprintf('crafting.%s.item', $type))) {
									$item = $cost->getItem();

									$output['item'] = [
										'id' => $item->getId(),
										'rarity' => $item->getRarity(),
										'carryLimit' => $item->getCarryLimit(),
										'value' => $item->getValue(),
										'buyPrice' => $item->getBuyPrice(),
										'sellPrice' => $item->getSellPrice(),
									];

									if (
										$projection->isAllowed(sprintf('crafting.%s.item.name', $type)) ||
										$projection->isAllowed(sprintf('crafting.%s.item.description', $type))
									) {
										/** @var ItemStrings $strings */
										$strings = $this->getStrings($item);

										$output['item'] += [
											'name' => $strings->getName(),
											'description' => $strings->getDescription(),
										];
									}
								}

								// endregion

								return $output;
							},
							$costs->toArray()
						);
					};

					$output['crafting'] = [
						'craftable' => $crafting->isCraftable(),
					];

					// region Previous Weapon Fields
					if ($projection->isAllowed('crafting.previous')) {
						$previous = $crafting->getPrevious();

						$output['crafting']['previous'] = $previous ? $previous->getId() : null;
					}
					// endregion

					// region Branches Fields
					if ($projection->isAllowed('crafting.branches')) {
						$output['crafting']['branches'] = array_map(
							function(Weapon $branch): int {
								return $branch->getId();
							},
							$crafting->getBranches()->toArray()
						);
					}
					// endregion

					// region Crafting Materials Fields
					if ($projection->isAllowed('crafting.craftingMaterials')) {
						$output['crafting']['craftingMaterials'] = call_user_func(
							$transformer,
							'craftingMaterials',
							$crafting->getCraftingMaterials()
						);
					}
					// endregion

					// region Upgrade Materials Fields
					if ($projection->isAllowed('crafting.upgradeMaterials')) {
						$output['crafting']['upgradeMaterials'] = call_user_func(
							$transformer,
							'upgradeMaterials',
							$crafting->getUpgradeMaterials()
						);
					}
					// endregion
				} else
					$output['crafting'] = null;
			}
			// endregion

			// region Assets Fields
			if ($projection->isAllowed('assets')) {
				$assets = $entity->getAssets();

				if ($assets) {
					$output['assets'] = [];

					// region Icon Fields
					if ($projection->isAllowed('assets.icon')) {
						$icon = $assets->getIcon();

						$output['assets']['icon'] = $icon ? $icon->getUri() : null;
					}
					// endregion

					// region Image Fields
					if ($projection->isAllowed('assets.image')) {
						$image = $assets->getImage();

						$output['assets']['image'] = $image ? $image->getUri() : null;
					}
					// endregion
				} else
					$output['assets'] = null;
			}

			// endregion

			return $output;
		}
	}