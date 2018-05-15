<?php
	namespace App\Controller;

	use App\Entity\Item;
	use DaybreakStudios\DozeBundle\ResponderService;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\Routing\RouterInterface;

	class ItemsDataController extends AbstractDataController {
		/**
		 * ItemsDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Item::class);
		}

		/**
		 * @param EntityInterface|Item|null $entity
		 *
		 * @param Projection                $projection
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $entity, Projection $projection): ?array {
			if (!$entity)
				return null;

			return [
				'id' => $entity->getId(),
				'name' => $entity->getName(),
				'description' => $entity->getName(),
				'rarity' => $entity->getRarity(),
				'carryLimit' => $entity->getCarryLimit(),
				'sellPrice' => $entity->getSellPrice(),
				'buyPrice' => $entity->getBuyPrice(),
			];
		}
	}