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
		 * @param EntityInterface|Item|null $item
		 *
		 * @return array|null
		 */
		protected function normalizeOne(?EntityInterface $item): ?array {
			if (!$item)
				return null;

			return [
				'id' => $item->getId(),
				'name' => $item->getName(),
				'description' => $item->getName(),
				'rarity' => $item->getRarity(),
				'carryLimit' => $item->getCarryLimit(),
				'sellPrice' => $item->getSellPrice(),
				'buyPrice' => $item->getBuyPrice(),
			];
		}
	}