<?php
	namespace App\Controller;

	use App\Contrib\Transformers\CharmTransformer;
	use App\Entity\Charm;
	use App\Entity\CharmRank;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\SkillRank;
	use App\Entity\Strings\CharmRankStrings;
	use App\Entity\Strings\CharmStrings;
	use App\Entity\Strings\ItemStrings;
	use App\Entity\Strings\SkillRankStrings;
	use App\Entity\Strings\SkillStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class CharmsController extends AbstractController {
		/**
		 * CharmsDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Charm::class);
		}

		/**
		 * @Route(path="/charms", methods={"GET"}, name="charms.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/charms", methods={"PUT"}, name="charms.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param CharmTransformer $transformer
		 * @param Request          $request
		 *
		 * @return Response
		 */
		public function create(CharmTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/charms/{charm<\d+>}", methods={"GET"}, name="charms.read")
		 *
		 * @param Request $request
		 * @param Charm   $charm
		 *
		 * @return Response
		 */
		public function read(Request $request, Charm $charm): Response {
			return $this->respond($request, $charm);
		}

		/**
		 * @Route(path="/charms/{charm<\d+>}", methods={"PATCH"}, name="charms.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param CharmTransformer $transformer
		 * @param Request          $request
		 * @param Charm            $charm
		 *
		 * @return Response
		 */
		public function update(CharmTransformer $transformer, Request $request, Charm $charm): Response {
			return $this->doUpdate($transformer, $charm, $request);
		}

		/**
		 * @Route(path="/charms/{charm<\d+>}", methods={"DELETE"}, name="charms.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param CharmTransformer $transformer
		 * @param Charm            $charm
		 *
		 * @return Response
		 */
		public function delete(CharmTransformer $transformer, Charm $charm): Response {
			return $this->doDelete($transformer, $charm);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Charm);

			$output = [
				'id' => $entity->getId(),
			];

			if ($projection->isAllowed('name')) {
				/** @var CharmStrings $strings */
				$strings = $this->getStrings($entity);

				$output['name'] = $strings->getName();
			}

			// region CharmRank Fields
			if ($projection->isAllowed('ranks')) {
				$output['ranks'] = array_map(
					function(CharmRank $rank) use ($projection): array {
						$output = [
							'level' => $rank->getLevel(),
							'rarity' => $rank->getRarity(),
						];

						if ($projection->isAllowed('ranks.name')) {
							/** @var CharmRankStrings $strings */
							$strings = $this->getStrings($rank);

							$output['name'] = $strings->getName();
						}

						// region SkillRank Fields
						if ($projection->isAllowed('ranks.skills')) {
							$output['skills'] = array_map(
								function(SkillRank $skillRank) use ($projection): array {
									$output = [
										'id' => $skillRank->getId(),
										'level' => $skillRank->getLevel(),
										'modifiers' => $skillRank->getModifiers() ?: new \stdClass(),
									];

									if ($projection->isAllowed('ranks.skills.description')) {
										/** @var SkillRankStrings $strings */
										$strings = $this->getStrings($skillRank);

										$output['description'] = $strings->getDescription();
									}

									if ($projection->isAllowed('ranks.skills.skill'))
										$output['skill'] = $skillRank->getSkill()->getId();

									if ($projection->isAllowed('ranks.skills.skillName')) {
										/** @var SkillStrings $strings */
										$strings = $this->getStrings($skillRank->getSkill());

										$output['skillName'] = $strings->getName();
									}

									return $output;
								},
								$rank->getSkills()->toArray()
							);
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
													'rarity' => $item->getRarity(),
													'carryLimit' => $item->getCarryLimit(),
													'value' => $item->getValue(),
													'buyPrice' => $item->getBuyPrice(),
													'sellPrice' => $item->getSellPrice(),
												];

												if (
													$projection->isAllowed('ranks.crafting.materials.item.name') ||
													$projection->isAllowed('ranks.crafting.materials.item.description')
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
					},
					$entity->getRanks()->toArray()
				);
			}

			// endregion

			return $output;
		}
	}