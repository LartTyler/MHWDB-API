<?php
	namespace App\Controller;

	use App\Entity\Armor;
	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\SkillRank;
	use App\Entity\Slot;
	use App\Game\Element;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\Routing\RouterInterface;

	class ArmorDataController extends AbstractDataController {
		/**
		 * ArmorDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Armor::class);
		}

		/**
		 * @param EntityInterface|Armor|null $entity
		 *
		 * @param Projection                 $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$armorSet = $entity->getArmorSet();
			$assets = $entity->getAssets();
			$crafting = $entity->getCrafting();

			$assetTransformer = function(?Asset $asset): ?string {
				return $asset ? $asset->getUri() : null;
			};

			return [
				'id' => $entity->getId(),
				'slug' => $entity->getSlug(),
				'name' => $entity->getName(),
				'type' => $entity->getType(),
				'rank' => $entity->getRank(),
				'rarity' => $entity->getRarity(),
				'defense' => $entity->getDefense(),
				'resistances' => $entity->getResistances(),
				'slots' => array_map(function(Slot $slot): array {
					return [
						'rank' => $slot->getRank(),
					];
				}, $entity->getSlots()->toArray()),
				// default to \stdClass to fix an empty array being returned instead of an empty object
				'attributes' => $entity->getAttributes() ?: new \stdClass(),
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
				}, $entity->getSkills()->toArray()),
				'armorSet' => $armorSet ? [
					'id' => $armorSet->getId(),
					'name' => $armorSet->getName(),
					'rank' => $armorSet->getRank(),
					'pieces' => array_map(function(Armor $armor): int {
						return $armor->getId();
					}, $armorSet->getPieces()->toArray()),
				] : $armorSet,
				'assets' => [
					'imageMale' => $assets ? call_user_func($assetTransformer, $assets->getImageMale()) : null,
					'imageFemale' => $assets ? call_user_func($assetTransformer, $assets->getImageFemale()) : null,
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
		}
	}