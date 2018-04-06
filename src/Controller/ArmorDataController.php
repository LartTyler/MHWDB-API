<?php
	namespace App\Controller;

	use App\Entity\Armor;
	use App\Entity\Asset;
	use App\Entity\Skill;
	use App\Utility\EntityUtil;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
	use DaybreakStudios\DozeBundle\ResponderService;
	use Doctrine\Common\Collections\Collection;
	use Symfony\Bridge\Doctrine\RegistryInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\RouterInterface;

	class ArmorDataController extends AbstractDataController {
		/**
		 * ArmorDataController constructor.
		 *
		 * @param RegistryInterface $doctrine
		 * @param ResponderService  $responder
		 * @param RouterInterface   $router
		 */
		public function __construct(RegistryInterface $doctrine, ResponderService $responder, RouterInterface $router) {
			parent::__construct($doctrine, $responder, $router, Armor::class);
		}

		/**
		 * {@inheritdoc}
		 */
		public function listAction(Request $request): Response {
			/** @var Armor[]|Response $items */
			$items = $this->doListAction($request);

			if ($items instanceof Response)
				return $items;

			return $this->respond($this->normalizeManyArmors($items));
		}

		/**
		 * {@inheritdoc}
		 */
		public function readAction(string $idOrSlug): Response {
			/** @var Armor|null|ApiErrorInterface $item */
			$item = $this->doReadAction($idOrSlug);

			if ($item instanceof ApiErrorInterface)
				return $this->respond($item);

			return $this->respond($this->normalizeOneArmor($item));
		}

		/**
		 * @param Armor[] $armors
		 *
		 * @return array
		 */
		protected function normalizeManyArmors(array $armors): array {
			return array_map((function(Armor $armor): array {
				return $this->normalizeOneArmor($armor);
			})->bindTo($this), $armors);
		}

		/**
		 * @param Armor|null $armor
		 *
		 * @return array|null
		 */
		protected function normalizeOneArmor(?Armor $armor): ?array {
			if (!$armor)
				return null;

			$assetTransformer = function(?Asset $asset): ?string {
				return $asset ? $asset->getUri() : null;
			};

			return EntityUtil::normalize($armor, [
				'id',
				'slug',
				'name',
				'type',
				'rank',
				'rarity',
				'attributes',
				'skills' => [
					'id',
					'slug',
					'skill' => function(Skill $skill): int {
						return $skill->getId();
					},
					'level',
					'description',
					'modifiers',
				],
				'armorSet' => [
					'id',
					'name',
					'rank',
					'pieces' => function(Armor $armor): int {
						return $armor->getId();
					},
				],
				'assets' => [
					'imageMale' => $assetTransformer,
					'imageFemale' => $assetTransformer,
				],
			]);
		}
	}