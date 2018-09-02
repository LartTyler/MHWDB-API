<?php
	namespace App\Export;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;

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
		public function getExporterForClass(string $class): ?ExporterInterface {
			return $this->exporters[$class] ?? null;
		}

		/**
		 * @param EntityInterface $entity
		 *
		 * @return Export
		 */
		public function export(EntityInterface $entity): Export {
			$exporter = $this->getExporterForClass($class = get_class($entity));

			if (!$exporter)
				throw new \InvalidArgumentException('Could not find exporter for ' . $class);

			return $exporter->export($entity);
		}
	}
