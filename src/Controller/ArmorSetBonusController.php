<?php
	namespace App\Controller;

	use App\Contrib\Transformers\ArmorSetBonusTransformer;
	use App\Entity\ArmorSetBonus;
	use App\Entity\ArmorSetBonusRank;
	use App\Entity\Strings\ArmorSetBonusStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class ArmorSetBonusController extends AbstractController {
		/**
		 * ArmorSetBonusDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, ArmorSetBonus::class);
		}

		/**
		 * @Route(path="/armor/sets/bonuses", methods={"GET"}, name="armor-set-bonuses.list")
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
		 * @param Request       $request
		 * @param ArmorSetBonus $bonus
		 *
		 * @return Response
		 */
		public function read(Request $request, ArmorSetBonus $bonus): Response {
			return $this->respond($request, $bonus);
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
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof ArmorSetBonus);

			$output = [
				'id' => $entity->getId(),
			];

			if ($projection->isAllowed('name')) {
				/** @var ArmorSetBonusStrings $strings */
				$strings = $this->getStrings($entity);

				$output['name'] = $strings->getName();
			}

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