<?php
	namespace App\Controller;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\SkillRank;
	use App\Entity\Slot;
	use App\Game\Element;
	use App\QueryDocument\Projection;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
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
		 * @param EntityInterface|ArmorSet|null $entity
		 * @param Projection                    $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$bonus = $entity->getBonus();

			$transformer = function(?Asset $asset): ?string {
				return $asset ? $asset->getUri() : null;
			};

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'rank' => $entity->getRank(),
			];

			if ($projection->isAllowed('pieces')) {
				$output['pieces'] = array_map(function(Armor $armor) use ($projection, $transformer): array {
					$defense = $armor->getDefense();
					$resists = $armor->getResistances();

					$output = [
						'id' => $armor->getId(),
						'slug' => $armor->getSlug(),
						'name' => $armor->getName(),
						'type' => $armor->getType(),
						'rank' => $armor->getRank(),
						'rarity' => $armor->getRarity(),
						// default to \stdClass to fix an empty array being returned instead of an empty object
						'attributes' => $armor->getAttributes() ?: new \stdClass(),
						'defense' => [
							'base' => $defense->getBase(),
							'max' => $defense->getMax(),
							'augmented' => $defense->getAugmented(),
						],
						'resistances' => [
							Element::FIRE => $resists->getFire(),
							Element::WATER => $resists->getWater(),
							Element::ICE => $resists->getIce(),
							Element::THUNDER => $resists->getThunder(),
							Element::DRAGON => $resists->getDragon(),
						],
					];

					if ($projection->isAllowed('pieces.slots')) {
						$output['slots'] = array_map(function(Slot $slot): array {
							return [
								'rank' => $slot->getRank(),
							];
						}, $armor->getSlots()->toArray());
					}

					if ($projection->isAllowed('pieces.skills')) {
						$output['skills'] = array_map(function(SkillRank $rank) use ($projection): array {
							$output = [
								'id' => $rank->getId(),
								'slug' => $rank->getSlug(),
								'level' => $rank->getLevel(),
								'description' => $rank->getDescription(),
								'modifiers' => $rank->getModifiers(),
								'skill' => $rank->getSkill()->getId(),
								'skillName' => $rank->getSkill()->getName(),
							];

							if ($projection->isAllowed('pieces.skills.skill'))
								$output['skill'] = $rank->getSkill()->getId();

							if ($projection->isAllowed('pieces.skills.skillName'))
								$output['skill'] = $rank->getSkill()->getName();

							return $output;
						}, $armor->getSkills()->toArray());
					}

					if ($projection->isAllowed('pieces.armorSet'))
						$output['armorSet'] = $armor->getArmorSet()->getId();

					if ($projection->isAllowed('pieces.assets')) {
						$assets = $armor->getAssets();

						if ($assets) {
							$output['assets'] = [];

							if ($projection->isAllowed('pieces.assets.imageMale'))
								$output['assets']['imageMale'] = call_user_func($transformer, $assets->getImageMale());

							if ($projection->isAllowed('pieces.assets.imageFemale'))
								$output['assets']['imageFemale'] = call_user_func($transformer, $assets->getImageFemale());
 						} else
 							$output['assets'] = null;
					}

					if ($projection->isAllowed('pieces.crafting')) {
						$crafting = $armor->getCrafting();

						if ($crafting) {
							$output['crafting'] = [];

							if ($projection->isAllowed('pieces.crafting.materials')) {
								$output['crafting']['materials'] = array_map(
									function(CraftingMaterialCost $cost) use ($projection): array {
										$output = [
											'quantity' => $cost->getQuantity(),
										];

										if ($projection->isAllowed('pieces.crafting.materials.item')) {
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
									}, $crafting->getMaterials()->toArray()
								);
							}
						} else
							$output['crafting'] = null;
					}

					return $output;
				}, $entity->getPieces()->toArray());
			}

			if ($projection->isAllowed('bonus')) {
				$bonus = $entity->getBonus();

				if ($bonus) {
					$output['bonus'] = [
						'id' => $bonus->getId(),
						'name' => $bonus->getName(),
					];

					if ($projection->isAllowed('bonus.ranks')) {
						$output['bonus']['ranks'] = array_map(
							function(ArmorSetBonusRank $rank) use ($projection): array {
								$output = [
									'pieces' => $rank->getPieces(),
								];

								if ($projection->isAllowed('bonus.ranks.skill')) {
									$skillRank = $rank->getSkill();

									$output['skill'] = [
										'id' => $skillRank->getId(),
										'slug' => $skillRank->getSlug(),
										'level' => $skillRank->getLevel(),
										'description' => $skillRank->getDescription(),
										'modifiers' => $skillRank->getModifiers(),
									];

									if ($projection->isAllowed('bonus.ranks.skill.skill'))
										$output['skill']['skill'] = $skillRank->getSkill()->getId();

									if ($projection->isAllowed('bonus.ranks.skill.skillName'))
										$output['skill']['skillName'] = $skillRank->getSkill()->getName();
								}

								return $output;
							}, $bonus->getRanks()->toArray()
						);
					}
				} else
					$output['bonus'] = null;
			}

			return $output;
		}
	}