<?php
	namespace App\Controller;

	use App\Contrib\Transformers\SkillTransformer;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Entity\Strings\SkillStrings;
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
			];

			if ($projection->isAllowed('name') || $projection->isAllowed('description')) {
				/** @var SkillStrings $strings */
				$strings = $this->getStrings($entity);

				$output += [
					'name' => $strings->getName(),
					'description' => $strings->getDescription(),
				];
			}

			// region SkillRank Fields
			if ($projection->isAllowed('ranks')) {
				$output['ranks'] = array_map(
					function(SkillRank $rank) use ($projection): array {
						$output = [
							'id' => $rank->getId(),
							'skill' => $rank->getSkill()->getId(),
							'level' => $rank->getLevel(),
							'description' => $rank->getDescription(),
							'modifiers' => $rank->getModifiers() ?: new \stdClass(),
						];

						if ($projection->isAllowed('ranks.skillName')) {
							/** @var SkillStrings $strings */
							$strings = $this->getStrings($rank->getSkill());

							$output['skillName'] = $strings->getName();
						}

						return $output;
					},
					$entity->getRanks()->toArray()
				);
			}

			// endregion

			return $output;
		}
	}