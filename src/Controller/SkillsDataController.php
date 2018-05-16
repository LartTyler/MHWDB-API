<?php
	namespace App\Controller;

	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\QueryDocument\Projection;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\Routing\RouterInterface;

	class SkillsDataController extends AbstractDataController {
		/**
		 * SkillsCrudController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Skill::class);
		}

		/**
		 * @param EntityInterface|Skill|null $entity
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
				'description' => $entity->getDescription(),
			];

			if ($projection->isAllowed('ranks')) {
				$output['ranks'] = array_map(function(SkillRank $rank): array {
					// No related field optimizations needed for each SkillRank, as the parent skill is already loaded

					return [
						'id' => $rank->getId(),
						'slug' => $rank->getSlug(),
						'skill' => $rank->getSkill()->getId(),
						'skillName' => $rank->getSkill()->getName(),
						'level' => $rank->getLevel(),
						'description' => $rank->getDescription(),
						'modifiers' => $rank->getModifiers(),
					];
				}, $entity->getRanks()->toArray());
			}

			return $output;
		}
	}