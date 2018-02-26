<?php
	namespace App\Controller;

	use App\Entity\Skill;
	use DaybreakStudios\DozeBundle\ResponderService;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Response;
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
	}