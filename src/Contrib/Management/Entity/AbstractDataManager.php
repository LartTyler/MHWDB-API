<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\Data\EntityDataInterface;
	use App\Contrib\DataManagerInterface;
	use App\Contrib\EntityType;
	use App\Contrib\Management\ContribGroup;
	use App\Import\ImporterInterface;
	use App\Import\ManagedDeleteInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

	abstract class AbstractDataManager implements DataManagerInterface {
		/**
		 * @var ContribGroup
		 */
		protected $contribGroup;

		/**
		 * @var ImporterInterface
		 */
		protected $importer;

		/**
		 * @var string
		 */
		protected $entityClass;

		/**
		 * @var string|null
		 */
		protected $dataClass;

		/**
		 * AbstractEntityManager constructor.
		 *
		 * @param ContribGroup      $contribGroup
		 * @param ImporterInterface $importer
		 */
		public function __construct(ContribGroup $contribGroup, ImporterInterface $importer) {
			$this->contribGroup = $contribGroup;
			$this->importer = $importer;

			$this->entityClass = $importer->getSupportedClass();
			$this->dataClass = EntityType::getDataClass(EntityType::ENTITY_CLASS_MAP[$this->entityClass] ?? null);

			if (!$this->dataClass) {
				throw new \InvalidArgumentException(
					$this->entityClass . ' must have an associated data class (in ' .
					EntityType::class . '::ENTITY_CLASS_MAP)'
				);
			}
		}

		/**
		 * @return string
		 */
		public function getEntityClass(): string {
			return $this->entityClass;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function export(EntityInterface $entity): void {
			$data = $this->createDataClassInstance($entity);

			$this->contribGroup->put($entity->getId(), $data->normalize(), $data->getEntityGroupName(true));
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $input
		 *
		 * @return void
		 */
		public function update(EntityInterface $entity, object $input): void {
			$data = $this->createDataClassInstance($entity);
			$data->update($input);

			$this->export($entity);
			$this->importer->import($entity, $this->contribGroup->get($entity->getId()));
		}

		/**
		 * @param int|null $id
		 * @param object   $input
		 *
		 * @return EntityInterface
		 */
		public function create(?int $id, object $input): EntityInterface {
			return $this->importer->create($id, $input);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return void
		 */
		public function delete(EntityInterface $entity): void {
			if (!($this->importer instanceof ManagedDeleteInterface))
				return;

			$this->importer->delete($entity);
		}

		/**
		 * @param EntityInterface|object $source
		 *
		 * @return EntityDataInterface
		 * @throws \InvalidArgumentException if the type of $source is not supported
		 */
		protected function createDataClassInstance($source): EntityDataInterface {
			if (!is_object($source)) {
				throw new \InvalidArgumentException(
					'Data objects can only be created from objects or instances of ' . EntityInterface::class
				);
			}

			if ($source instanceof EntityInterface)
				$type = 'Entity';
			else
				$type = 'Json';

			return call_user_func([$this->dataClass, 'from' . $type], $source);
		}
	}