<?php
	namespace App\Loaders\Schemas;

	use App\Loaders\SchemaInterface;

	class RecoveryMethodSchema implements SchemaInterface {
		/**
		 * @var string
		 */
		protected $type;

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * RecoverySchema constructor.
		 *
		 * @param string $type
		 * @param string $name
		 */
		public function __construct(string $type, string $name) {
			$this->type = $type;
			$this->name = $name;
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
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @param array $data
		 *
		 * @return static
		 */
		public static function create(array $data) {
			return new static($data['type'], $data['name']);
		}
	}