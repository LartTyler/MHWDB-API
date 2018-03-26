<?php
	namespace App\Utility;

	use App\Entity\Armor;
	use App\Entity\ArmorSet;
	use App\Entity\Skill;
	use App\Entity\SkillRank;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\Collection;

	final class EntityUtil {
		/**
		 * @param SkillRank|SkillRank[] $subject
		 * @param array         $fields
		 *
		 * @return array
		 */
		public static function normalizeSkillRank($subject, array $fields = []): array {
			return self::normalize($subject, $fields ?: [
				'id' => true,
				'slug' => true,
				'skill' => [function(Skill $skill): int {
					return $skill->getId();
				}],
				'description' => true,
				'level' => true,
				'modifiers' => true,
			]);
		}

		/**
		 * @param ArmorSet|ArmorSet[] $subject
		 * @param array               $fields
		 *
		 * @return array
		 */
		public static function normalizeArmorSet($subject, array $fields = []): array {
			return self::normalize($subject, $fields ?: [
				'id' => true,
				'name' => true,
				'rank' => true,
				'pieces' => [function(Collection $pieces): array {
					return array_map(function(Armor $armor): int {
						return $armor->getId();
					}, $pieces->toArray());
				}],
			]);
		}

		/**
		 * @param Armor[]|Armor $subject
		 * @param array         $fields
		 *
		 * @return array
		 */
		public static function normalizeArmor($subject, array $fields = []): array {
			return self::normalize($subject, $fields ?: [
				'id' => true,
				'slug' => true,
				'name' => true,
				'type' => true,
				'rank' => true,
				'attributes' => true,
				'skills' => [self::class, 'normalizeSkillRank'],
				'armorSet' => [self::class, 'normalizeArmorSet'],
			]);
		}

		/**
		 * @param EntityInterface|EntityInterface[] $data
		 * @param array                             $fields
		 *
		 * @return array|mixed
		 */
		public static function normalizeEntityOrCollection($data, array $fields = []) {
			if ($data instanceof EntityInterface) {
				$name = self::getEntityName(get_class($data));
				$method = 'normalize' . $name;

				if (method_exists(self::class, $method))
					return call_user_func([self::class, $method], $data);
			} else if (is_iterable($data)) {
				$items = [];

				foreach ($data as $datum)
					$items[] = self::normalizeEntityOrCollection($data, $fields);

				return $items;
			}

			return $data;
		}

		/**
		 * @param EntityInterface|EntityInterface[] $subject
		 * @param array                             $fields
		 *
		 * @return array
		 */
		public static function normalize($subject, array $fields): array {
			if (is_iterable($subject)) {
				$items = [];

				foreach ($subject as $item)
					$items[] = self::normalize($item, $fields);

				return $items;
			} else if (!($subject instanceof EntityInterface))
				throw new \InvalidArgumentException('Cannot normalize ' .
					(is_object($subject) ? get_class($subject) : gettype($subject)));

			if (!$subject)
				return null;

			$normal = [];

			foreach ($fields as $field => $normalizer) {
				$getter = 'get' . ucfirst($field);

				if (!method_exists($subject, $getter))
					throw new \InvalidArgumentException('No field named ' . $field . ' exists on ' .
						get_class($subject));

				$value = call_user_func([$subject, $getter]);

				if (is_array($normalizer) && sizeof($normalizer) >= 1) {
					if ($normalizer[0] instanceof \Closure) {
						$callable = $normalizer[0];
						$args = array_slice($normalizer, 1);
					} else {
						$callable = [$normalizer[0], $normalizer[1]];
						$args = array_slice($normalizer, 2);
					}

					$value = call_user_func_array($callable, [$value, $args]);
				}

				$normal[$field] = $value;
			}

			return $normal;
		}

		/**
		 * @param string $class
		 *
		 * @return string
		 */
		public static function getEntityName(string $class): string {
			if ($pos = strrpos($class, '\\'))
				return substr($class, $pos + 1);

			return $class;
		}

		/**
		 * EntityUtil constructor.
		 */
		private function __construct() {
		}
	}