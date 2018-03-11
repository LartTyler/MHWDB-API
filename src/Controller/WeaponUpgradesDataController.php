<?php
	namespace App\Controller;

	use App\Entity\Weapon;
	use App\Entity\WeaponUpgradeNode;
	use DaybreakStudios\DozeBundle\ResponderService;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\RouterInterface;

	class WeaponUpgradesDataController extends AbstractDataController {
		/**
		 * UpgradeNodesDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, WeaponUpgradeNode::class);
		}

		/**
		 * @param string $idOrSlug
		 *
		 * @return Response
		 * @throws \Doctrine\ORM\NonUniqueResultException
		 */
		public function forWeaponAction(string $idOrSlug): Response {
			/** @var Weapon|null $weapon */
			$weapon = $this->manager->createQueryBuilder()
				->from('App:Weapon', 'w')
				->select('w')
				->where('w.id = :idOrSlug')
				->orWhere('w.slug = :idOrSlug')
				->setParameter('idOrSlug', $idOrSlug)
				->setMaxResults(1)
				->getQuery()
					->getOneOrNullResult();

			if (!$weapon)
				return $this->responder->createNotFoundResponse();

			$node = $weapon->getUpgradeNode();

			return $this->respond([
				'id' => $node->getId(),
				'weapon' => $weapon->getId(),
				'craftable' => $node->isCraftable(),
				'previous' => $this->previousNodeToArray($node->getPrevious()),
				'branches' => $node->getBranches(),
			]);
		}

		/**
		 * @param WeaponUpgradeNode|null $node
		 *
		 * @return array|null
		 */
		public function previousNodeToArray(?WeaponUpgradeNode $node): ?array {
			if (!$node)
				return null;

			return [
				'id' => $node->getId(),
				'weapon' => $node->getWeapon()->getId(),
				'craftable' => $node->isCraftable(),
				'previous' => $this->previousNodeToArray($node->getPrevious()),
			];
		}
	}