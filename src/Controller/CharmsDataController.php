<?php
	namespace App\Controller;

	use App\Entity\Charm;
	use DaybreakStudios\DozeBundle\ResponderService;
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
	}