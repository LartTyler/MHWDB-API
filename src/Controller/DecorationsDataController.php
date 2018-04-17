<?php
	namespace App\Controller;

	use App\Entity\Decoration;
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
				'skill' => $decoration->getSkill()->getId(),
				'slot' => $decoration->getSlot(),
			];
		}
	}