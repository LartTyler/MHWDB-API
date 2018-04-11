<?php
	namespace App\Scraping\Scrapers\Helpers;

	class EldersealData {
		/**
		 * @var array
		 */
		private $data;

		/**
		 * EldersealData constructor.
		 *
		 * @param string $path
		 */
		public function __construct(string $path) {
			$this->data = json_decode(file_get_contents($path), true);
		}

		/**
		 * @param string $weaponType
		 *
		 * @return array
		 */
		public function get(string $weaponType): array {
			return $this->data[$weaponType] ?? [];
		}
	}