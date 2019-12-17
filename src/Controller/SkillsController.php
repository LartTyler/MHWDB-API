<?php
	namespace App\Controller;

	use App\Contrib\Transformers\SkillTransformer;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\DoctrineQueryDocument\Projection\Projection;
	use DaybreakStudios\DoctrineQueryDocument\QueryManagerInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	class SkillsController extends AbstractController {
		/**
		 * SkillsCrudController constructor.
		 *
		 * @param QueryManagerInterface $queryManager
		 */
		public function __construct(QueryManagerInterface $queryManager) {
			parent::__construct($queryManager, Skill::class);
		}

		/**
		 * @Route(path="/skills", methods={"GET"}, name="skills.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return $this->doList($request);
		}

		/**
		 * @Route(path="/skills", methods={"PUT"}, name="skills.create")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param SkillTransformer $transformer
		 * @param Request          $request
		 *
		 * @return Response
		 */
		public function create(SkillTransformer $transformer, Request $request): Response {
			return $this->doCreate($transformer, $request);
		}

		/**
		 * @Route(path="/skills/{skill<\d+>}", methods={"GET"}, name="skills.read")
		 *
		 * @param Request $request
		 * @param Skill   $skill
		 *
		 * @return Response
		 */
		public function read(Request $request, Skill $skill): Response {
			return $this->respond($request, $skill);
		}

		/**
		 * @Route(path="/skills/{skill<\d+>}", methods={"PATCH"}, name="skills.update")
		 * @IsGranted("ROLE_EDITOR")
		 *
		 * @param SkillTransformer $transformer
		 * @param Request          $request
		 * @param Skill            $skill
		 *
		 * @return Response
		 */
		public function update(SkillTransformer $transformer, Request $request, Skill $skill): Response {
			return $this->doUpdate($transformer, $skill, $request);
		}

		/**
		 * @Route(path="/skills/{skill<\d+>}", methods={"DELETE"}, name="skills.delete")
		 *
		 * @param SkillTransformer $transformer
		 * @param Skill            $skill
		 *
		 * @return Response
		 */
		public function delete(SkillTransformer $transformer, Skill $skill): Response {
			return $this->doDelete($transformer, $skill);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function normalizeOne(EntityInterface $entity, Projection $projection): array {
			assert($entity instanceof Skill);

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'description' => $entity->getDescription(),
			];

			// region SkillRank Fields
			if ($projection->isAllowed('ranks')) {
				$output['ranks'] = array_map(
					function(SkillRank $rank): array {
						// No related field optimizations needed for each SkillRank, as the parent skill is already loaded

						return [
							'id' => $rank->getId(),
							'skill' => $rank->getSkill()->getId(),
							'skillName' => $rank->getSkill()->getName(),
							'level' => $rank->getLevel(),
							'description' => $rank->getDescription(),
							'modifiers' => $rank->getModifiers() ?: new \stdClass(),
						];
					},
					$entity->getRanks()->toArray()
				);
			}
			// endregion

			return $output;
		}
	}