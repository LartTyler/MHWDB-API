<?php
	namespace App\Controller;

	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\SkillRank;
	use App\QueryDocument\Projection;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\Routing\RouterInterface;

	class CharmsDataController extends AbstractDataController {
		/**
		 * CharmsDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Charm::class);
		}

		/**
		 * @param EntityInterface|Charm|null $entity
		 * @param Projection                 $projection
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
			];

			if ($projection->isAllowed('ranks')) {
				$output['ranks'] = array_map(function(CharmRank $rank) use ($projection): array {
					$output = [
						'name' => $rank->getName(),
						'level' => $rank->getLevel(),
						'rarity' => $rank->getRarity(),
					];

					if ($projection->isAllowed('ranks.skills')) {
						$output['skills'] = array_map(function(SkillRank $skillRank) use ($projection): array {
							$output = [
								'id' => $skillRank->getId(),
								'slug' => $skillRank->getSlug(),
								'level' => $skillRank->getLevel(),
								'description' => $skillRank->getDescription(),
								'modifiers' => $skillRank->getModifiers(),
							];

							if ($projection->isAllowed('ranks.skills.skill'))
								$output['skill'] = $skillRank->getSkill()->getId();

							if ($projection->isAllowed('ranks.skills.skillName'))
								$output['skillName'] = $skillRank->getSkill()->getName();

							return $output;
						}, $rank->getSkills()->toArray());
					}

					if ($projection->isAllowed('ranks.crafting')) {
						$crafting = $rank->getCrafting();

						if ($crafting) {
							$output['crafting'] = [
								'craftable' => $crafting->isCraftable(),
							];

							if ($projection->isAllowed('ranks.crafting.materials')) {
								$output['crafting']['materials'] = array_map(
									function(CraftingMaterialCost $cost) use ($projection): array {
										$output = [
											'quantity' => $cost->getQuantity(),
										];

										if ($projection->isAllowed('ranks.crafting.materials.item')) {
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
				}, $entity->getRanks()->toArray());
			}

			return $output;
		}
	}