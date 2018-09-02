<?php
	namespace App\Export;

	use App\Entity\Asset;
	use App\Entity\CraftingMaterialCost;
	use App\Entity\Slot;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;

	final class ExportHelper {
		/**
		 * ExportHelper constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param Collection|CraftingMaterialCost[] $costs
		 *
		 * @return array
		 */
		public static function toSimpleCostArray(Collection $costs): array {
			return $costs->map(function(CraftingMaterialCost $cost): array {
				return [
					'item' => $cost->getItem()->getId(),
					'quantity' => $cost->getQuantity(),
				];
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
				'uri' => $asset->getUri(),
				'primaryHash' => $asset->getPrimaryHash(),
				'secondaryHash' => $asset->getSecondaryHash(),
			];
		}

		/**
		 * @param EntityInterface|null $entity
		 *
		 * @return int|null
		 */
		public static function toReference(?EntityInterface $entity): ?int {
			return $entity ? $entity->getId() : null;
		}

		/**
		 * @param Collection $collection
		 *
		 * @return int[]
		 */
		public static function toReferenceArray(Collection $collection): array {
			return $collection->map(function(EntityInterface $entity): int {
				return $entity->getId();
			})->toArray();
		}
	}