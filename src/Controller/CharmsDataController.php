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
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
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
		 * @Route(path="/charms", methods={"GET"}, name="charms.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/charms/{idOrSlug}", methods={"GET"}, name="charms.read")
		 *
		 * @param string $idOrSlug
		 *
		 * @return Response
		 */
		public function read(string $idOrSlug): Response {
			return parent::read($idOrSlug);
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

			// region CharmRank Fields
			if ($projection->isAllowed('ranks')) {
				$output['ranks'] = array_map(function(CharmRank $rank) use ($projection): array {
					$output = [
						'name' => $rank->getName(),
						'level' => $rank->getLevel(),
						'rarity' => $rank->getRarity(),
					];

					// region SkillRank Fields
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
					// endregion

					// region Crafting Fields
					if ($projection->isAllowed('ranks.crafting')) {
						$crafting = $rank->getCrafting();

						if ($crafting) {
							$output['crafting'] = [
								'craftable' => $crafting->isCraftable(),
							];

							// region CraftingMaterialCost Fields
							if ($projection->isAllowed('ranks.crafting.materials')) {
								$output['crafting']['materials'] = array_map(
									function(CraftingMaterialCost $cost) use ($projection): array {
										$output = [
											'quantity' => $cost->getQuantity(),
										];

										// region Item Fields
										if ($projection->isAllowed('ranks.crafting.materials.item')) {
											$item = $cost->getItem();

											$output['item'] = [
												'id' => $item->getId(),
												'name' => $item->getName(),
												'description' => $item->getDescription(),
												'rarity' => $item->getRarity(),
												'carryLimit' => $item->getCarryLimit(),
												'value' => $item->getValue(),
											];
										}
										// endregion

										return $output;
									}, $crafting->getMaterials()->toArray()
								);
							}
							// endregion
						} else
							$output['crafting'] = null;
					}
					// endregion

					return $output;
				}, $entity->getRanks()->toArray());
			}
			// endregion

			return $output;
		}
	}