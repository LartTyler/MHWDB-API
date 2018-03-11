<?php
	namespace App\Controller;

	use App\Entity\Weapon;
	use App\Entity\WeaponUpgradeNode;
	use DaybreakStudios\DozeBundle\ResponderService;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\RouterInterface;

	class WeaponsDataController extends AbstractDataController {
		/**
		 * WeaponsDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Weapon::class);
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return Response
		 */
		public function upgradeTreeAction(string $idOrSlug): Response {
			/** @var Weapon|null $weapon */
			$weapon = $this->getEntity($idOrSlug);

			if (!$weapon)
				return $this->responder->createNotFoundResponse();

			$node = $weapon->getUpgradeNode();

			return $this->respond($this->nodeToArray($node));
		}

		/**
		 * @param WeaponUpgradeNode $node
		 *
		 * @return array
		 */
		protected function nodeToArray(WeaponUpgradeNode $node): array {
			return [
				'id' => $node->getId(),
				'weapon' => $node->getWeapon()->getId(),
				'craftable' => $node->isCraftable(),
				'previous' => $node->getPrevious()->getId(),
				'branches' => array_map(function(WeaponUpgradeNode $branch) {
					return $branch->getId();
				}, $node->getBranches()->toArray()),
			];
		}
	}