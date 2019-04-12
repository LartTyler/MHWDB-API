<?php
	namespace App\Loaders\Schemas;

	use App\Loaders\SchemaInterface;

	class LocationSchema implements SchemaInterface {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var int
		 */
		protected $zoneCount;

		/**
		 * @var CampSchema[]
		 */
		protected $camps;

		/**
		 * LocationSchema constructor.
		 *
		 * @param string $name
		 * @param int    $zoneCount
		 * @param array  $camps
		 */
		public function __construct(string $name, int $zoneCount, array $camps) {
			$this->name = $name;
			$this->zoneCount = $zoneCount;
			$this->camps = array_map(function(array $data): CampSchema {
				return CampSchema::create($data);
			}, $camps);
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
		public function getZoneCount(): int {
			return $this->zoneCount;
		}

		/**
		 * @return CampSchema[]
		 */
		public function getCamps(): array {
			return $this->camps;
		}

		/**
		 * @param array $data
		 *
		 * @return static
		 */
		public static function create(array $data) {
			return new static($data['name'], (int)$data['zoneCount'], $data['camps']);
		}
	}