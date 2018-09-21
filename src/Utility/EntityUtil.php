<?php
	namespace App\Utility;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Proxy\Proxy;

	final class EntityUtil {
		/**
		 * EntityUtil constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return string
		 */
		public static function getRealClass(EntityInterface $entity): string {
			if ($entity instanceof Proxy)
				return get_parent_class($entity);

			return get_class($entity);
		}
	}