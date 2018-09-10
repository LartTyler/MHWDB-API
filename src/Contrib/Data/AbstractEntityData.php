<?php
	namespace App\Contrib\Data;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;

	abstract class AbstractEntityData implements EntityDataInterface {
		/**
		 * @return array
		 */
		public function normalize(): array {
			$output = $this->doNormalize();

			ksort($output);

			return $output;
		}

		/**
		 * @return array
		 */
		protected abstract function doNormalize(): array;

		/**
		 * @param EntityInterface[]|Collection $collection
		 *
		 * @return int[]
		 */
		protected static function toIdArray(Collection $collection): array {
			return $collection->map(function(EntityInterface $entity): int {
				return $entity->getId();
			})->toArray();
		}
	}