<?php
	namespace App\Controller;

	use App\Entity\Decoration;
	use App\Entity\SkillRank;
	use App\QueryDocument\Projection;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Routing\RouterInterface;

	class DecorationsDataController extends AbstractDataController {
		/**
		 * DecorationsDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Decoration::class);
		}

		/**
		 * @Route(path="/decorations", methods={"GET"}, name="decorations.list")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function list(Request $request): Response {
			return parent::list($request);
		}

		/**
		 * @Route(path="/decorations/{decoration<\d+>}", methods={"GET"}, name="decorations.read")
		 *
		 * @param Decoration $decoration
		 *
		 * @return Response
		 */
		public function read(Decoration $decoration): Response {
			return $this->respond($decoration);
		}

		/**
		 * @param EntityInterface|Decoration|null $entity
		 * @param Projection                      $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			$output = [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'rarity' => $entity->getRarity(),
				'slot' => $entity->getSlot(),
			];

			// region SkillRank Fields
			if ($projection->isAllowed('skills')) {
				$output['skills'] = array_map(function(SkillRank $rank) use ($projection): array {
					$output = [
						'id' => $rank->getId(),
						'description' => $rank->getDescription(),
						'level' => $rank->getLevel(),
						'modifiers' => $rank->getModifiers(),
					];

					if ($projection->isAllowed('skills.skill'))
						$output['skill'] = $rank->getSkill()->getId();

					if ($projection->isAllowed('skills.skillName'))
						$output['skillName'] = $rank->getSkill()->getName();

					return $output;
				}, $entity->getSkills()->toArray());
			}
			// endregion

			return $output;
		}
	}