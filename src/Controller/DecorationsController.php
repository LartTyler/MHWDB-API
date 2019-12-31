<?php
	namespace App\Controller;

	use App\Contrib\Transformers\DecorationTransformer;
	use App\Entity\Decoration;
	use App\Entity\SkillRank;
	use App\Entity\Strings\DecorationStrings;
	use App\Entity\Strings\SkillRankStrings;
	use App\Entity\Strings\SkillStrings;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class DecorationsController extends AbstractController {
		/**
		 * DecorationsDataController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Decoration::class);
		}

		/**
		 * @Route(path="/decorations", methods={"GET"}, name="decorations.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/decorations", methods={"PUT"}, name="decorations.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param DecorationTransformer $transformer
		 * @param Request               $request
		 *
		 * @return Response
		 */
		public function create(DecorationTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/decorations/{decoration<\d+>}", methods={"GET"}, name="decorations.read")
		 *
		 * @param Request    $request
		 * @param Decoration $decoration
		 *
		 * @return Response
		 */
		public function read(Request $request, Decoration $decoration): Response {
			return $this->respond($request, $decoration);
		}

		/**
		 * @Route(path="/decorations/{decoration<\d+>}", methods={"PATCH"}, name="decorations.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param DecorationTransformer $transformer
		 * @param Request               $request
		 * @param Decoration            $decoration
		 *
		 * @return Response
		 */
		public function update(DecorationTransformer $transformer, Request $request, Decoration $decoration): Response {
			return $this->doUpdate($transformer, $decoration, $request);
		}

		/**
		 * @Route(path="/decorations/{decoration<\d+>}", methods={"DELETE"}, name="decorations.delete")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param DecorationTransformer $transformer
		 * @param Decoration            $decoration
		 *
		 * @return Response
		 */
		public function delete(DecorationTransformer $transformer, Decoration $decoration): Response {
			return $this->doDelete($transformer, $decoration);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Decoration);

			$output = [
				'id' => $entity->getId(),
				'rarity' => $entity->getRarity(),
				'slot' => $entity->getSlot(),
			];

			if ($projection->isAllowed('name')) {
				/** @var DecorationStrings $strings */
				$strings = $this->getStrings($entity);

				$output['name'] = $strings->getName();
			}

			// region SkillRank Fields
			if ($projection->isAllowed('skills')) {
				$output['skills'] = array_map(
					function(SkillRank $rank) use ($projection): array {
						$output = [
							'id' => $rank->getId(),
							'level' => $rank->getLevel(),
							'modifiers' => $rank->getModifiers(),
						];

						if ($projection->isAllowed('skills.description')) {
							/** @var SkillRankStrings $strings */
							$strings = $this->getStrings($rank);

							$output['description'] = $strings->getDescription();
						}

						if ($projection->isAllowed('skills.skill'))
							$output['skill'] = $rank->getSkill()->getId();

						if ($projection->isAllowed('skills.skillName')) {
							/** @var SkillStrings $strings */
							$strings = $this->getStrings($rank->getSkill());

							$output['skillName'] = $strings->getName();
						}

						return $output;
					},
					$entity->getSkills()->toArray()
				);
			}

			// endregion

			return $output;
		}
	}