<?php
	namespace App\Controller;

	use App\Entity\Decoration;
	use App\Entity\SkillRank;
	use App\Response\Projection;
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
		 * @param EntityInterface|Decoration|null $entity
		 * @param Projection                      $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			return [
				'id' => $entity->getId(),
				'slug' => $entity->getSlug(),
				'name' => $entity->getName(),
				'rarity' => $entity->getRarity(),
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
				}, $entity->getSkills()->toArray()),
				'slot' => $entity->getSlot(),
			];
		}
	}