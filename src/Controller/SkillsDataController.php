<?php
	namespace App\Controller;

	use App\Entity\Skill;
	use App\Entity\SkillRank;
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
		 * @param EntityInterface|Skill|null $skill
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $skill): ?array {
			if (!$skill)
				return null;

			return [
				'id' => $skill->getId(),
				'slug' => $skill->getSlug(),
				'name' => $skill->getName(),
				'description' => $skill->getDescription(),
				'ranks' => array_map(function(SkillRank $rank) use ($skill): array {
					return [
						'id' => $rank->getId(),
						'slug' => $rank->getSlug(),
						'skill' => $skill->getId(),
						'skillName' => $skill->getId(),
						'level' => $rank->getLevel(),
						'description' => $rank->getDescription(),
						'modifiers' => $rank->getModifiers(),
					];
				}, $skill->getRanks()->toArray()),
			];
		}
	}