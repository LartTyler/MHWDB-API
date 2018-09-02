<?php
	namespace App\Export\Exporters;

	use App\Export\ExporterInterface;

	abstract class AbstractExporter implements ExporterInterface {
		/**
		 * @var string
		 */
		protected $supportedClass;

		/**
		 * AbstractExporter constructor.
		 *
		 * @param string $supportedClass
		 */
		public function __construct(string $supportedClass) {
			$this->supportedClass = $supportedClass;
		}

		/**
		 * @return string
		 */
		public function getSupportedClass(): string {
			return $this->supportedClass;
		}
	}