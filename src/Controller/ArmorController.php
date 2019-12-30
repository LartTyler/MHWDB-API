<?php
	namespace App\Controller;

	use App\Contrib\Transformers\ArmorTransformer;
	use App\Entity\Armor;
	use App\Entity\ArmorSlot;
	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\SkillRank;
	use App\Entity\Strings\ArmorSetStrings;
	use App\Entity\Strings\ArmorStrings;
	use App\Entity\Strings\ItemStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class ArmorController extends AbstractController {
		/**
		 * ArmorDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Armor::class);
		}

		/**
		 * @Route(path="/armor", methods={"GET"}, name="armor.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList(
				$request,
				[
					'strings.language' => $request->getLocale(),
				]
			);
		}

		/**
		 * @Route(path="/armor", methods={"PUT"}, name="armor.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ArmorTransformer $transformer
		 * @param Request          $request
		 *
		 * @return Response
		 */
		public function create(ArmorTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/armor/{armor<\d+>}", methods={"GET"}, name="armor.read")
		 *
		 * @param Request $request
		 * @param Armor   $armor
		 *
		 * @return Response
		 */
		public function read(Request $request, Armor $armor): Response {
			return $this->respond($request, $armor);
		}

		/**
		 * @Route(path="/armor/{armor<\d+>}", methods={"PATCH"}, name="armor.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ArmorTransformer $transformer
		 * @param Request          $request
		 * @param Armor            $armor
		 *
		 * @return Response
		 */
		public function update(ArmorTransformer $transformer, Request $request, Armor $armor): Response {
			return $this->doUpdate($transformer, $armor, $request);
		}

		/**
		 * @Route(path="/armor/{armor<\d+>}", methods={"DELETE"}, name="armor.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ArmorTransformer $transformer
		 * @param Armor            $armor
		 *
		 * @return Response
		 */
		public function delete(ArmorTransformer $transformer, Armor $armor): Response {
			return $this->doDelete($transformer, $armor);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Armor);

			$defense = $entity->getDefense();
			$resists = $entity->getResistances();

			$output = [
				'id' => $entity->getId(),
				'type' => $entity->getType(),
				'rank' => $entity->getRank(),
				'rarity' => $entity->getRarity(),
				'defense' => [
					'base' => $defense->getBase(),
					'max' => $defense->getMax(),
					'augmented' => $defense->getAugmented(),
				],
				'resistances' => [
					'fire' => $resists->getFire(),
					'water' => $resists->getWater(),
					'ice' => $resists->getIce(),
					'thunder' => $resists->getThunder(),
					'dragon' => $resists->getDragon(),
				],
				// default to \stdClass to fix an empty array being returned instead of an empty object
				'attributes' => $entity->getAttributes() ?: new \stdClass(),
			];

			if ($projection->isAllowed('name')) {
				/** @var ArmorStrings $strings */
				$strings = $this->getStrings($entity);

				$output['name'] = $strings->getName();
			}

			// region Slots Fields
			if ($projection->isAllowed('slots')) {
				$output['slots'] = array_map(
					function(ArmorSlot $slot): array {
						return [
							'rank' => $slot->getRank(),
						];
					},
					$entity->getSlots()->toArray()
				);
			}
			// endregion

			// region Skills Fields
			if ($projection->isAllowed('skills')) {
				$output['skills'] = array_map(
					function(SkillRank $rank) use ($projection): array {
						$output = [
							'id' => $rank->getId(),
							'level' => $rank->getLevel(),
							'description' => $rank->getDescription(),
							'modifiers' => $rank->getModifiers() ?: new \stdClass(),
						];

						if ($projection->isAllowed('skills.skill'))
							$output['skill'] = $rank->getSkill()->getId();

						if ($projection->isAllowed('skills.skillName'))
							$output['skillName'] = $rank->getSkill()->getName();

						return $output;
					},
					$entity->getSkills()->toArray()
				);
			}
			// endregion

			// region ArmorSet Fields
			if ($projection->isAllowed('armorSet')) {
				$armorSet = $entity->getArmorSet();

				if ($armorSet) {
					$output['armorSet'] = [
						'id' => $armorSet->getId(),
						'rank' => $armorSet->getRank(),
					];

					if ($projection->isAllowed('armorSet.name')) {
						/** @var ArmorSetStrings $strings */
						$strings = $this->getStrings($armorSet);

						$output['armorSet']['name'] = $strings->getName();
					}

					if ($projection->isAllowed('armorSet.pieces')) {
						$output['armorSet']['pieces'] = array_map(
							function(Armor $armor): int {
								return $armor->getId();
							},
							$armorSet->getPieces()->toArray()
						);
					}

					if ($projection->isAllowed('armorSet.bonus')) {
						$bonus = $armorSet->getBonus();

						$output['armorSet']['bonus'] = $bonus ? $bonus->getId() : null;
					}
				} else
					$output['armorSet'] = null;
			}
			// endregion

			// region Assets Fields
			if ($projection->isAllowed('assets')) {
				$assets = $entity->getAssets();

				if ($assets) {
					$output['assets'] = [];

					$transformer = function(?Asset $asset): ?string {
						return $asset ? $asset->getUri() : null;
					};

					if ($projection->isAllowed('assets.imageMale'))
						$output['assets']['imageMale'] = call_user_func($transformer, $assets->getImageMale());

					if ($projection->isAllowed('assets.imageFemale'))
						$output['assets']['imageFemale'] = call_user_func($transformer, $assets->getImageFemale());
				} else
					$output['assets'] = null;
			}
			// endregion

			// region Crafting Fields
			if ($projection->isAllowed('crafting')) {
				$crafting = $entity->getCrafting();

				if ($crafting) {
					$output['crafting'] = [];

					// region CraftingMaterialCost Fields
					if ($projection->isAllowed('crafting.materials')) {
						$output['crafting']['materials'] = array_map(
							function(CraftingMaterialCost $cost) use ($projection): array {
								$output = [
									'quantity' => $cost->getQuantity(),
								];

								// region Item Fields
								if ($projection->isAllowed('crafting.materials.item')) {
									$item = $cost->getItem();

									$output['item'] = [
										'id' => $item->getId(),
										'rarity' => $item->getRarity(),
										'carryLimit' => $item->getCarryLimit(),
										'value' => $item->getValue(),
									];

									if (
										$projection->isAllowed('crafting.materials.item.name') ||
										$projection->isAllowed('crafting.materials.item.description')
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
							$crafting->getMaterials()->toArray()
						);
					}
					// endregion
				} else
					$output['crafting'] = null;
			}

			// endregion

			return $output;
		}
	}