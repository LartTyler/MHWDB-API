<?php
	namespace App\Loaders\Schemas;

	use App\Loaders\SchemaInterface;

	class CampSchema implements SchemaInterface {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var int
		 */
		protected $zone;

		protected function __construct(string $name, int $zone) {
			$this->name = $name;
			$this->zone = $zone;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return int
		 */
		public function getZone(): int {
			return $this->zone;
		}

		/**
		 * @param array $data
		 *
		 * @return static
		 */
		public static function create(array $data) {
			return new static($data['name'], (int)$data['zone']);
		}
	}