<?php
	namespace App\Controller;

	use App\Entity\Armor;
	use App\Entity\Asset;
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
		 * @param EntityInterface|Armor|null $armor
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $armor): ?array {
			if (!$armor)
				return null;

			$armorSet = $armor->getArmorSet();
			$assets = $armor->getAssets();

			$assetTransformer = function(?Asset $asset): ?string {
				return $asset ? $asset->getUri() : null;
			};

			return [
				'id' => $armor->getId(),
				'slug' => $armor->getSlug(),
				'name' => $armor->getName(),
				'type' => $armor->getType(),
				'rank' => $armor->getRank(),
				'rarity' => $armor->getRarity(),
				'resistances' => $armor->getResistances(),
				'slots' => array_map(function(Slot $slot): array {
					return [
						'rank' => $slot->getRank(),
					];
				}, $armor->getSlots()->toArray()),
				'attributes' => $armor->getAttributes(),
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
			];
		}
	}