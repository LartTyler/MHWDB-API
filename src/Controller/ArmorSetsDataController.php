<?php
	namespace App\Controller;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\SkillRank;
	use App\Entity\Slot;
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
		 * @param EntityInterface|ArmorSet|null $armorSet
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $armorSet): ?array {
			if (!$armorSet)
				return null;

			$bonus = $armorSet->getBonus();

			$transformer = function(?Asset $asset): ?string {
				return $asset ? $asset->getUri() : null;
			};

			return [
				'id' => $armorSet->getId(),
				'name' => $armorSet->getName(),
				'rank' => $armorSet->getRank(),
				'pieces' => array_map(function(Armor $armor) use ($transformer): array {
					$assets = $armor->getAssets();
					$crafting = $armor->getCrafting();

					return [
						'id' => $armor->getId(),
						'slug' => $armor->getSlug(),
						'name' => $armor->getName(),
						'type' => $armor->getType(),
						'rank' => $armor->getRank(),
						'rarity' => $armor->getRarity(),
						// default to \stdClass to fix an empty array being returned instead of an empty object
						'attributes' => $armor->getAttributes() ?: new \stdClass(),
						'defense' => $armor->getDefense(),
						'resistances' => $armor->getResistances(),
						'slots' => array_map(function(Slot $slot): array {
							return [
								'rank' => $slot->getRank(),
							];
						}, $armor->getSlots()->toArray()),
						'skills' => array_map(function(SkillRank $rank): array {
							return [
								'id' => $rank->getId(),
								'slug' => $rank->getSlug(),
								'level' => $rank->getLevel(),
								'description' => $rank->getDescription(),
								'modifiers' => $rank->getModifiers(),
								'skill' => $rank->getSkill()->getId(),
								'skillName' => $rank->getSkill()->getName(),
							];
						}, $armor->getSkills()->toArray()),
						'armorSet' => $armor->getArmorSet()->getId(),
						'assets' => [
							'imageMale' => $assets ? call_user_func($transformer, $assets->getImageMale()) : null,
							'imageFemale' => $assets ? call_user_func($transformer, $assets->getImageFemale()) : null,
						],
						'crafting' => $crafting ? [
							'materials' => array_map(function(CraftingMaterialCost $cost): array {
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
							}, $crafting->getMaterials()->toArray()),
						] : null,
					];
				}, $armorSet->getPieces()->toArray()),
				'bonus' => $bonus ? [
					'id' => $bonus->getId(),
					'name' => $bonus->getName(),
					'ranks' => array_map(function(ArmorSetBonusRank $rank): array {
						$skillRank = $rank->getSkill();

						return [
							'pieces' => $rank->getPieces(),
							'skill' => [
								'id' => $skillRank->getId(),
								'slug' => $skillRank->getSlug(),
								'level' => $skillRank->getLevel(),
								'description' => $skillRank->getDescription(),
								'modifiers' => $skillRank->getModifiers(),
								'skill' => $skillRank->getSkill()->getId(),
								'skillName' => $skillRank->getSkill()->getName(),
							],
						];
					}, $bonus->getRanks()->toArray()),
				] : null,
			];
		}
	}