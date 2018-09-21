<?php
	namespace App\Doctrine;

	use App\Doctrine\Id\ConditionalAssignedGenerator;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Event\LifecycleEventArgs;
	use Doctrine\ORM\Id\AssignedGenerator;
	use Doctrine\ORM\Mapping\ClassMetadata;

	/**
	 * Adapted from {@see https://github.com/tseho/doctrine-assigned-identity}.
	 *
	 * @package App\Doctrine
	 */
	class AssignedIdentityListener {
		/**
		 * @param LifecycleEventArgs $args
		 *
		 * @return void
		 */
		public function prePersist(LifecycleEventArgs $args): void {
			$entity = $args->getEntity();

			if (!($entity instanceof EntityInterface))
				return;

			$metadata = $args->getEntityManager()->getClassMetadata(get_class($entity));

			if ($metadata->idGenerator instanceof AssignedGenerator)
				return;

			$metadata->generatorType = ClassMetadata::GENERATOR_TYPE_CUSTOM;
			$metadata->idGenerator = new ConditionalAssignedGenerator($metadata->idGenerator);
		}
	}