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
		public function readAction(string $idOrSlug): Response {
			/** @var Weapon|null $entity */
			$entity = $this->getEntity($idOrSlug);

			return $this->respond($this->normalizeEntity($entity));
		}

		/**
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function listAction(Request $request): Response {
			if ($request->query->has('q')) {
				$query = $request->query->all();

				if (isset($query['q']))
					$query['q'] = $query = $this->denormalizeQueryFields(json_decode($query['q'], true));

				$results = $this->getSearchResults($query);

				if ($results instanceof Response)
					return $results;

				return $this->respond($this->normalizeEntityArray($results));
			}

			$items = $this->manager->getRepository($this->entityClass)->findAll();

			return $this->responder->createResponse($this->normalizeEntityArray($items));
		}

		/**
		 * @param array|null $array
		 *
		 * @return array
		 */
		protected function normalizeEntityArray(?array $array): array {
			if (!$array)
				return [];

			$self = $this;

			return array_map(function(Weapon $weapon) use ($self): array {
				return $self->normalizeEntity($weapon);
			}, $array);
		}

		/**
		 * @param Weapon|null $weapon
		 *
		 * @return array|null
		 */
		public function normalizeEntity(?Weapon $weapon): ?array {
			if (!$weapon)
				return null;

			if ($node = $weapon->getUpgradeNode())
				$craftingInfo = [
					'craftable' => $node->isCraftable(),
					'previous' => $node->getPrevious() ? $node->getPrevious()->getWeapon()->getId() : null,
					'branches' => array_map(function(WeaponUpgradeNode $branch) {
						return $branch->getWeapon()->getId();
					}, $node->getBranches()->toArray()),
				];
			else
				$craftingInfo = [];

			return [
				'id' => $weapon->getId(),
				'name' => $weapon->getName(),
				'slug' => $weapon->getSlug(),
				'type' => $weapon->getType(),
				'rarity' => $weapon->getRarity(),
				'attributes' => $weapon->getAttributes(),
				'crafting' => $craftingInfo,
			];
		}

		/**
		 * @param array $query
		 *
		 * @return array
		 */
		protected function denormalizeQueryFields(array $query): array {
			$out = [];

			foreach ($query as $key => $value) {
				if (is_array($value))
					$value = $this->denormalizeQueryFields($query);

				$key = str_replace([
					'crafting.',
					'upgradeNode.previous.',
				], [
					'upgradeNode.',
					'upgradeNode.previous.weapon.',
				], $key);

				$out[$key] = $value;
			}

			return $out;
		}
	}