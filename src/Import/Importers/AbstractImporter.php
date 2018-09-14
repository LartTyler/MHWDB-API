<?php
	namespace App\Import\Importers;

	use App\Import\ImporterInterface;

	abstract class AbstractImporter implements ImporterInterface {
		/**
		 * @var string
		 */
		protected $supportedClass;

		/**
		 * AbstractImporter constructor.
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

		/**
		 * @return \InvalidArgumentException
		 */
		protected function createCannotImportException(): \InvalidArgumentException {
			return new \InvalidArgumentException(static::class . ' can only import ' . $this->getSupportedClass() .
				' instances');
		}

		/**
		 * @param string $where
		 * @param string $class
		 * @param int    $id
		 *
		 * @return \RuntimeException
		 */
		protected function createMissingReferenceException(string $where, string $class, int $id): \RuntimeException {
			return new \RuntimeException('Could not find related reference to ' . $class . ' with ID ' . $id . '(in ' .
				$where . ')');
		}

		/**
		 * @param string $uri
		 *
		 * @return \RuntimeException
		 */
		protected function createAssetNotFoundException(string $uri): \RuntimeException {
			return new \RuntimeException('The asset at ' . $uri .
				' could not be matched to an asset in the data repository.');
		}
	}