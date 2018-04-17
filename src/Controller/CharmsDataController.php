<?php
	namespace App\Controller;

	use App\Entity\Charm;
	use App\Entity\SkillRank;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
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
		 * @param EntityInterface|Charm|null $decoration
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $decoration): ?array {
			if (!$decoration)
				return null;

			return [
				'id' => $decoration->getId(),
				'slug' => $decoration->getSlug(),
				'name' => $decoration->getName(),
				'skills' => array_map(function(SkillRank $rank): array {
					return [
						'id' => $rank->getId(),
						'slug' => $rank->getSlug(),
						'skill' => $rank->getSkill()->getId(),
						'skillName' => $rank->getSkill()->getName(),
						'level' => $rank->getLevel(),
						'description' => $rank->getDescription(),
						'modifiers' => $rank->getModifiers(),
					];
				}, $decoration->getSkills()->toArray()),
			];
		}
	}