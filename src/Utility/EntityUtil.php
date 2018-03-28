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
		 * @var ReflectionAttributeCache|null
		 */
		private static $attributeCache = null;

		/**
		 * @param object|array $subject
		 * @param array        $fields
		 * @param bool         $isNested
		 *
		 * @return array
		 */
		public static function normalize($subject, array $fields = [], $isNested = false): array {
			$normalized = [];

			if (!$isNested && is_iterable($subject)) {
				foreach ($subject as $item)
					$normalized[] = self::normalize($item, $fields, true);

				return $normalized;
			}

			if (!$fields)
				$fields = array_keys(self::getAttributes($subject));

			foreach ($fields as $field => $argument) {
				if (is_int($field)) {
					$field = $argument;
					$argument = true;
				}

				$value = self::getAttributeValue($field, $subject);

				if (is_array($argument)) {
					if (is_iterable($value)) {
						$children = [];

						foreach ($value as $item)
							$children[] = self::normalize($item, $argument, true);

						$value = $children;
					} else
						$value = self::normalize($value, $argument, true);
				} else if ($argument instanceof \Closure) {
					if (is_iterable($value)) {
						$children = [];

						foreach ($value as $item)
							$children[] = call_user_func($argument, $item);

						$value = $children;
					} else
						$value = call_user_func($argument, $value);
				}

				$normalized[$field] = $value;
			}

			return $normalized;
		}

		/**
		 * @param object $subject
		 *
		 * @return \ReflectionMethod[]
		 */
		public static function getAttributes($subject): array {
			if (!self::$attributeCache)
				self::$attributeCache = new ReflectionAttributeCache();

			if ($cached = self::$attributeCache->getAll($subject))
				return $cached;

			$attributes = [];
			$refl = new \ReflectionClass($subject);

			foreach ($refl->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
				if (
					$method->getNumberOfRequiredParameters() > 0 ||
					$method->isConstructor() ||
					$method->isDestructor() ||
					$method->isStatic()
				) {
					continue;
				}

				$name = $method->getName();

				if (strpos($name, 'get') === 0 || strpos($name, 'has') === 0)
					$attributeName = lcfirst(substr($name, 3));
				else if (strpos($name, 'is') === 0)
					$attributeName = lcfirst(substr($name, 2));
				else
					continue;

				$attributes[$attributeName] = $method;
			}

			self::$attributeCache->setAll($subject, $attributes);

			return $attributes;
		}

		/**
		 * @param string $attribute
		 * @param object $subject
		 *
		 * @return mixed
		 */
		public static function getAttributeValue(string $attribute, $subject) {
			if (!self::$attributeCache)
				self::$attributeCache = new ReflectionAttributeCache();

			if ($accessor = self::$attributeCache->get($subject, $attribute))
				return $accessor->invoke($subject);

			$attributes = self::getAttributes($subject);

			if (!isset($attributes[$attribute]))
				throw new \InvalidArgumentException('No field named ' . $attribute . ' exists on ' . get_class($subject));

			return $attributes[$attribute]->invoke($subject);
		}

		/**
		 * EntityUtil constructor.
		 */
		private function __construct() {
		}
	}