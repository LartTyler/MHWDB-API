<?php
	namespace App\Controller;

	use App\Entity\Item;
	use DaybreakStudios\DozeBundle\ResponderService;
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
	}