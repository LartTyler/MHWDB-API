<?php
	namespace App\Doctrine;

	use App\Entity\LengthCachingEntityInterface;
	use Doctrine\ORM\Event\LifecycleEventArgs;

	class LengthCachingListener {
		/**
		 * @param LifecycleEventArgs $event
		 *
		 * @return void
		 */
		public function prePersist(LifecycleEventArgs $event): void {
			$this->handle($event);
		}

		/**
		 * @param LifecycleEventArgs $event
		 *
		 * @return void
		 */
		public function preUpdate(LifecycleEventArgs $event): void {
			$this->handle($event);
		}

		/**
		 * @param LifecycleEventArgs $event
		 *
		 * @return void
		 */
		public function handle(LifecycleEventArgs $event): void {
			$entity = $event->getEntity();

			if ($entity instanceof LengthCachingEntityInterface)
				$entity->syncLengthFields();
		}
	}