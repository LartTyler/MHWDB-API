<?php
	namespace App\Controller;

	use App\Entity\Decoration;
	use App\Entity\SkillRank;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
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
		 * @param EntityInterface|Decoration|null $decoration
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
				'rarity' => $decoration->getRarity(),
				'skills' => array_map(function(SkillRank $rank): array {
					return [
						'id' => $rank->getId(),
						'slug' => $rank->getSlug(),
						'description' => $rank->getDescription(),
						'level' => $rank->getLevel(),
						'skill' => $rank->getSkill()->getId(),
						'skillName' => $rank->getSkill()->getName(),
						'modifiers' => $rank->getModifiers(),
					];
				}, $decoration->getSkills()->toArray()),
				'slot' => $decoration->getSlot(),

				// DEPRECATED This field preserves BC for < 1.9.0 and will be removed in the future
				'skill' => $decoration->getSkill()->getId(),
			];
		}
	}