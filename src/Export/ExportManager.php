<?php
	namespace App\Export;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Proxy\Proxy;

	class ExportManager {
		/**
		 * @var ExporterInterface[]
		 */
		protected $exporters = [];

		/**
		 * ExportManager constructor.
		 *
		 * @param array $exporters
		 */
		public function __construct(array $exporters) {
			$this->addExporters($exporters);
		}

		/**
		 * @param array $exporters
		 *
		 * @return $this
		 */
		public function addExporters(array $exporters) {
			foreach ($exporters as $exporter)
				$this->addExporter($exporter);

			return $this;
		}

		/**
		 * @param ExporterInterface $exporter
		 *
		 * @return $this
		 */
		public function addExporter(ExporterInterface $exporter) {
			$this->exporters[$exporter->getSupportedClass()] = $exporter;

			return $this;
		}

		/**
		 * @param string $class
		 *
		 * @return ExporterInterface|null
		 */
		public function getExporter(string $class): ?ExporterInterface {
			return $this->exporters[$class] ?? null;
		}

		/**
		 * @param object $object
		 *
		 * @return ExporterInterface|null
		 */
		public function findExporter(object $object): ?ExporterInterface {
			if ($exporter = $this->getExporter(get_class($object)))
				return $exporter;
			else if ($object instanceof Proxy)
				return $this->getExporter(get_parent_class($object));

			return null;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return Export
		 */
		public function export(EntityInterface $entity): Export {
			$exporter = $this->findExporter($entity);

			if (!$exporter)
				throw new \InvalidArgumentException('Could not find exporter for ' . get_class($entity));

			return $exporter->export($entity);
		}
	}
