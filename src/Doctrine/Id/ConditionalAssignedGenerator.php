<?php
	namespace App\Doctrine\Id;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\EntityManager;
	use Doctrine\ORM\Id\AbstractIdGenerator;
	use Doctrine\ORM\Id\AssignedGenerator;

	/**
	 * Adapted from {@see https://github.com/tseho/doctrine-assigned-identity}.
	 *
	 * @package App\Doctrine\Id
	 */
	class ConditionalAssignedGenerator extends AssignedGenerator {
		/**
		 * @var AbstractIdGenerator
		 */
		protected $generator;

		/**
		 * ConditionalAssignedGenerator constructor.
		 *
		 * @param AbstractIdGenerator $generator
		 */
		public function __construct(AbstractIdGenerator $generator) {
			$this->generator = $generator;
		}

		/**
		 * @param EntityManager $em
		 * @param               $entity
		 *
		 * @return mixed
		 */
		public function generate(EntityManager $em, $entity) {
			if ($entity instanceof EntityInterface && $entity->getId() !== null)
				return parent::generate($em, $entity);

			$metadata = $em->getClassMetadata(get_class($entity));

			$id = [$metadata->getSingleIdentifierFieldName() => $this->generator->generate($em, $entity)];
			$metadata->setIdentifierValues($entity, $id);

			return $id;
		}
	}