<?php
	namespace App\Controller;

	use App\Entity\Decoration;
	use App\Entity\SkillRank;
	use App\QueryDocument\Projection;
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

			$output = [
				'id' => $entity->getId(),
				'slug' => $entity->getSlug(),
				'name' => $entity->getName(),
				'rarity' => $entity->getRarity(),
				'slot' => $entity->getSlot(),
			];

			// region SkillRank Fields
			if ($projection->isAllowed('skills')) {
				$output['skills'] = array_map(function(SkillRank $rank) use ($projection): array {
					$output = [
						'id' => $rank->getId(),
						'slug' => $rank->getSlug(),
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