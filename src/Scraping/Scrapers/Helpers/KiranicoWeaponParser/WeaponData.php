<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

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
		 * @var string|null
		 */
		protected $craftingPrevious = null;

		/**
		 * @var array
		 */
		protected $craftingMaterials = [];

		/**
		 * @var array
		 */
		protected $upgradeMaterials = [];

		/**
		 * @var bool
		 */
		protected $craftable = false;

		/**
		 * @return string|null
		 */
		public function getName(): ?string {
			return $this->name;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name) {
			$this->name = $this->fixWeaponName($name);

			return $this;
		}

		/**
		 * @return int|null
		 */
		public function getRarity(): ?int {
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

		/**
		 * @return null|string
		 */
		public function getCraftingPrevious(): ?string {
			return $this->craftingPrevious;
		}

		/**
		 * @param null|string $craftingPrevious
		 *
		 * @return $this
		 */
		public function setCraftingPrevious(string $craftingPrevious) {
			$this->craftingPrevious = $this->fixWeaponName($craftingPrevious);

			return $this;
		}

		/**
		 * @return array
		 */
		public function getUpgradeMaterials(): array {
			return $this->upgradeMaterials;
		}

		/**
		 * @param array $upgradeMaterials
		 *
		 * @return $this
		 */
		public function setUpgradeMaterials(array $upgradeMaterials) {
			$this->upgradeMaterials = $upgradeMaterials;

			return $this;
		}

		/**
		 * @return array
		 */
		public function getCraftingMaterials(): array {
			return $this->craftingMaterials;
		}

		/**
		 * @param array $craftingMaterials
		 *
		 * @return $this
		 */
		public function setCraftingMaterials(array $craftingMaterials) {
			$this->craftingMaterials = $craftingMaterials;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function isCraftable(): bool {
			return $this->craftable;
		}

		/**
		 * @param bool $craftable
		 *
		 * @return $this
		 */
		public function setCraftable(bool $craftable) {
			$this->craftable = $craftable;

			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return string
		 */
		protected function fixWeaponName(string $name): string {
			return str_replace([
				'Berseker'
			], [
				'Berserker'
			], $name);
		}
	}