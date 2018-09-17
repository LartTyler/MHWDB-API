<?php
	namespace App\Import;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Proxy\Proxy;

	class ImportManager {
		/**
		 * @var ImporterInterface[]
		 */
		protected $importers = [];

		/**
		 * ImportManager constructor.
		 *
		 * @param ImporterInterface[] $importers
		 */
		public function __construct(array $importers) {
			$this->setImporters($importers);
		}

		/**
		 * @param string $class
		 *
		 * @return ImporterInterface|null
		 */
		public function getImporter(string $class): ?ImporterInterface {
			if ($class instanceof Proxy)
				$class = get_parent_class($class);

			return $this->importers[$class] ?? null;
		}

		/**
		 * @param array $importers
		 *
		 * @return $this
		 */
		public function setImporters(array $importers) {
			$this->importers = [];

			foreach ($importers as $importer)
				$this->addImporter($importer);

			return $this;
		}

		/**
		 * @param ImporterInterface $importer
		 *
		 * @return $this
		 */
		public function addImporter(ImporterInterface $importer) {
			$this->importers[$importer->getSupportedClass()] = $importer;

			return $this;
		}

		/**
		 * @param EntityInterface $entity
		 * @param object          $data
		 *
		 * @return $this
		 */
		public function import(EntityInterface $entity, object $data) {
			$importer = $this->getImporter($class = get_class($entity));

			if (!$importer)
				throw new \InvalidArgumentException('No importer found for ' . $class);

			$importer->import($entity, $data);

			return $this;
		}

		/**
		 * @param string $class
		 * @param string $id
		 * @param object $data
		 *
		 * @return EntityInterface
		 */
		public function create(string $class, string $id, object $data): EntityInterface {
			$importer = $this->getImporter($class);

			if (!$importer)
				throw new \InvalidArgumentException('No importer found for ' . $class);

			return $importer->create($id, $data);
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return $this
		 */
		public function delete(EntityInterface $entity) {
			$importer = $this->getImporter(get_class($entity));

			if ($importer && $importer instanceof ManagedDeleteInterface)
				$importer->delete($entity);

			return $this;
		}
	}