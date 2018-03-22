<?php
	namespace App\Scraper\Kiranico\Scrapers\Helpers\Weapons;

	use App\Entity\AttributableTrait;
	use App\Game\WeaponType;

	class WeaponData {
		use AttributableTrait;

		/**
		 * @var string|null
		 */
		protected $name = null;

		/**
		 * @var int|null
		 */
		protected $rarity = null;

		/**
		 * @return string|null
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name) {
			$this->name = $name;

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getRarity() {
			return $this->rarity;
		}

		/**
		 * @param int $rarity
		 *
		 * @return $this
		 */
		public function setRarity(int $rarity) {
			$this->rarity = $rarity;

			return $this;
		}
	}