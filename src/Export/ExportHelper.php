<?php
	namespace App\Export;

	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Symfony\Component\Routing\RouterInterface;

	class ExportHelper {
		/**
		 * @var RouterInterface
		 */
		protected $router;

		/**
		 * ExportHelper constructor.
		 *
		 * @param RouterInterface $router
		 */
		public function __construct(RouterInterface $router) {
			$this->router = $router;
		}

		/**
		 * @param EntityInterface|null $entity
		 * @param string               $route
		 * @param string               $paramName
		 *
		 * @return null|string
		 */
		public function getReference(?EntityInterface $entity, string $route, string $paramName = 'id'): ?string {
			if (!$entity)
				return null;

			return $this->router->generate($route, [
				$paramName => $entity->getId(),
			]);
		}

		/**
		 * @param Collection $entities
		 * @param string     $route
		 * @param string     $paramName
		 *
		 * @return string[]
		 */
		public function getReferenceArray(Collection $entities, string $route, string $paramName = 'id'): array {
			$self = $this;
			$router = $this->router;

			return $entities->map(function(EntityInterface $entity) use ($self, $router, $route, $paramName): string {
				return $this->router->generate($route, [
					$paramName => $entity->getId(),
				]);
			})->toArray();
		}

		/**
		 * @param Collection $costs
		 *
		 * @return array
		 */
		public function toSimpleCostArray(Collection $costs): array {
			$router = $this->router;

			return $costs->map(function(CraftingMaterialCost $cost) use ($router): array {
				return [
					'item' => $router->generate('items.read', [
						'id' => $cost->getItem()->getId(),
					]),
					'quantity' => $cost->getQuantity(),
				];
			})->toArray();
		}

		/**
		 * @param SkillRank $rank
		 *
		 * @return array
		 */
		public function toSimpleSkillRank(SkillRank $rank): array {
			return [
				'level' => $rank->getLevel(),
				'skill' => $this->router->generate('skills.read', [
					'idOrSlug' => $rank->getSkill()->getId(),
				]),
			];
		}

		/**
		 * @param Collection $ranks
		 *
		 * @return array
		 */
		public function toSimpleSkillRankArray(Collection $ranks): array {
			$self = $this;

			return $ranks->map(function(SkillRank $rank) use ($self): array {
				return $self->toSimpleSkillRank($rank);
			})->toArray();
		}

		/**
		 * @param Asset|null $asset
		 *
		 * @return array
		 */
		public static function toSimpleAsset(?Asset $asset): ?array {
			if (!$asset)
				return null;

			return [
				'primaryHash' => $asset->getPrimaryHash(),
				'secondaryHash' => $asset->getSecondaryHash(),
				'uri' => $asset->getUri(),
			];
		}
	}