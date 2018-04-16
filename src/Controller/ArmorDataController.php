<?php
	namespace App\Controller;

	use App\Entity\Armor;
	use App\Entity\Asset;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use App\Utility\EntityUtil;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
	use DaybreakStudios\DozeBundle\ResponderService;
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

			$armorSet = $armor->getArmorSet();
			$assets = $armor->getAssets();

			$assetTransformer = function(?Asset $asset): ?string {
				return $asset ? $asset->getUri() : null;
			};

			return [
				'id' => $armor->getId(),
				'slug' => $armor->getSlug(),
				'name' => $armor->getName(),
				'type' => $armor->getType(),
				'rank' => $armor->getRank(),
				'rarity' => $armor->getRarity(),
				'attributes' => $armor->getAttributes(),
				'skills' => array_map(function(SkillRank $rank): array {
					return [
						'id' => $rank->getId(),
						'slug' => $rank->getSlug(),
						'level' => $rank->getLevel(),
						'description' => $rank->getDescription(),
						'modifiers' => $rank->getModifiers(),
						'skill' => $rank->getSkill()->getId(),
						'skillName' => $rank->getSkill()->getName(),
					];
				}, $armor->getSkills()->toArray()),
				'armorSet' => $armorSet ? [
					'id' => $armorSet->getId(),
					'name' => $armorSet->getName(),
					'rank' => $armorSet->getRank(),
					'pieces' => array_map(function(Armor $armor): int {
						return $armor->getId();
					}, $armorSet->getPieces()->toArray()),
				] : $armorSet,
				'assets' => [
					'imageMale' => $assets ? call_user_func($assetTransformer, $assets->getImageMale()) : null,
					'imageFemale' => $assets ? call_user_func($assetTransformer, $assets->getImageFemale()) : null,
				],
			];
		}
	}