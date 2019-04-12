<?php
	namespace App\Loaders\Loaders;

	use App\Loaders\LoaderInterface;
	use App\Loaders\SchemaInterface;

	abstract class AbstractLoader implements LoaderInterface {
		/**
		 * @var string
		 */
		protected $type;

		/**
		 * @var string
		 */
		protected $sourcePath;

		/**
		 * @var string
		 */
		protected $schemaClass;

		/**
		 * AbstractLoader constructor.
		 *
		 * @param string $type
		 * @param string $sourcePath
		 * @param string $schemaClass
		 */
		public function __construct(string $type, string $sourcePath, string $schemaClass) {
			if (!is_a($schemaClass, SchemaInterface::class, true))
				throw new \InvalidArgumentException($schemaClass . ' does not implement ' . SchemaInterface::class);

			$this->type = $type;
			$this->sourcePath = $sourcePath;
			$this->schemaClass = $schemaClass;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @return string
		 */
		public function getSourcePath(): string {
			return $this->sourcePath;
		}

		/**
		 * @return string
		 */
		public function getSchemaClass(): string {
			return $this->schemaClass;
		}

		/**
		 * @return SchemaInterface[]
		 */
		protected function read(): array {
			$data = @json_decode(file_get_contents($this->getSourcePath()), true);

			if (json_last_error() !== JSON_ERROR_NONE)
				throw new \RuntimeException('Could not decode JSON data: ' . json_last_error_msg());

			$class = $this->getSchemaClass();

			return array_map(function(array $data) use ($class): SchemaInterface {
				return call_user_func([$class, 'create'], $data);
			}, $data);
		}
	}