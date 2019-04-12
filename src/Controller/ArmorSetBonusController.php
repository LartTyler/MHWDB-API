<?php
	namespace App\Controller;

	use App\Contrib\Transformers\ArmorSetBonusTransformer;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\QueryDocument\Projection;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class ArmorSetBonusController extends AbstractController {
		/**
		 * ArmorSetBonusDataController constructor.
		 */
		public function __construct() {
			parent::__construct(ArmorSetBonus::class);
		}

		/**
		 * @Route(path="/armor/sets/bonuses", methods={"GET"}, name="armor-set-bonuses.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/armor/sets/bonuses", methods={"PUT"}, name="armor-set-bonuses.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ArmorSetBonusTransformer $transformer
		 * @param Request                  $request
		 *
		 * @return Response
		 */
		public function create(ArmorSetBonusTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/armor/sets/bonuses/{bonus<\d+>}", methods={"GET"}, name="armor-set-bonuses.read")
		 *
		 * @param ArmorSetBonus $bonus
		 *
		 * @return Response
		 */
		public function read(ArmorSetBonus $bonus): Response {
			return $this->respond($bonus);
		}

		/**
		 * @Route(path="/armor/sets/bonuses/{bonus<\d+>}", methods={"PATCH"}, name="armor-set-bonuses.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ArmorSetBonusTransformer $transformer
		 * @param Request                  $request
		 * @param ArmorSetBonus            $bonus
		 *
		 * @return Response
		 */
		public function update(
			ArmorSetBonusTransformer $transformer,
			Request $request,
			ArmorSetBonus $bonus
		): Response {
			return $this->doUpdate($transformer, $bonus, $request);
		}

		/**
		 * @Route(path="/armor/sets/bonuses/{bonus<\d+>}", methods={"DELETE"}, name="armor-set-bonuses.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param ArmorSetBonusTransformer $transformer
		 * @param ArmorSetBonus            $bonus
		 *
		 * @return Response
		 */
		public function delete(ArmorSetBonusTransformer $transformer, ArmorSetBonus $bonus): Response {
			return $this->doDelete($transformer, $bonus);
		}

		/**
		 * @param ArmorSetBonus|EntityInterface|null $entity
		 * @param Projection                         $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
			];

			if ($projection->isAllowed('ranks')) {
				$output['ranks'] = array_map(
					function(ArmorSetBonusRank $rank) use ($projection): array {
						$output = [
							'pieces' => $rank->getPieces(),
						];

						if ($projection->isAllowed('ranks.skill')) {
							$skill = $rank->getSkill();

							$output['skill'] = [
								'id' => $skill->getId(),
								'level' => $skill->getLevel(),
								'description' => $skill->getDescription(),
								'modifiers' => $skill->getModifiers(),
							];

							if ($projection->isAllowed('ranks.skill.skill'))
								$output['skill']['skill'] = $skill->getSkill()->getId();

							if ($projection->isAllowed('ranks.skill.skillName'))
								$output['skill']['skillName'] = $skill->getSkill()->getName();
						}

						return $output;
					},
					$entity->getRanks()->toArray()
				);
			}

			return $output;
		}
	}