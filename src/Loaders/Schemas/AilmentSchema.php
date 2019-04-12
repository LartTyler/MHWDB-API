<?php
	namespace App\Loaders\Schemas;

	use App\Loaders\SchemaInterface;

	class AilmentSchema implements SchemaInterface {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var string
		 */
		protected $description;

		/**
		 * @var RecoveryMethodSchema[]
		 */
		protected $recovery;

		/**
		 * @var ProtectionMethodSchema[]|array
		 */
		protected $protection;

		/**
		 * AilmentSchema constructor.
		 *
		 * @param string                   $name
		 * @param string                   $description
		 * @param RecoveryMethodSchema[]   $recovery
		 * @param ProtectionMethodSchema[] $protection
		 */
		public function __construct(string $name, string $description, array $recovery, array $protection) {
			$this->name = $name;
			$this->description = $description;
			$this->recovery = $recovery;
			$this->protection = $protection;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return string
		 */
		public function getDescription(): string {
			return $this->description;
		}

		/**
		 * @return RecoveryMethodSchema[]
		 */
		public function getRecovery(): array {
			return $this->recovery;
		}

		/**
		 * @return ProtectionMethodSchema[]
		 */
		public function getProtection() {
			return $this->protection;
		}

		/**
		 * @param array $data
		 *
		 * @return static
		 */
		public static function create(array $data) {
			$recovery = array_map(function(array $data): RecoveryMethodSchema {
				return RecoveryMethodSchema::create($data);
			}, $data['recovery']);

			$protection = array_map(function(array $data): ProtectionMethodSchema {
				return ProtectionMethodSchema::create($data);
			}, $data['protection']);

			return new static($data['name'], $data['description'], $recovery, $protection);
		}
	}