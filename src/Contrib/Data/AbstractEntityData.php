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

		/**
		 * @param string $expected
		 *
		 * @return \InvalidArgumentException
		 */
		protected static function createLoadFailedException(string $expected) {
		    return new \InvalidArgumentException(static::class . '  can only load ' . $expected . ' entities');
		}

		/**
		 * Utility method to normalize many instances of {@see EntityDataInterface}.
		 *
		 * @param EntityDataInterface[] $data
		 *
		 * @return array
		 */
		protected static function normalizeArray(array $data): array {
			return array_map(function(EntityDataInterface $datum): array {
				return $datum->normalize();
			}, $data);
		}

		/**
		 * @param object[] $array
		 *
		 * @return static[]
		 */
		public static function fromJsonArray(array $array): array {
			return array_map(function(object $rank): static {
				return static::fromJson($rank);
			}, $array);
		}

		/**
		 * @param EntityInterface[]|Collection $collection
		 *
		 * @return static[]
		 */
		public static function fromEntityCollection(Collection $collection): array {
			return $collection->map(function(EntityInterface $entity): static {
				return static::fromEntity($entity);
			})->toArray();
		}
	}