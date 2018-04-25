<?php
	namespace App\Scraping\Scrapers\Helpers\KiranicoWeaponParser;

	use App\Game\Element as ElementType;

	class Element {
		/**
		 * @var string
		 */
		protected $type = null;

		/**
		 * @var int
		 */
		protected $damage = null;

		/**
		 * @var bool
		 */
		protected $hidden = false;

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @param string $type
		 *
		 * @return $this
		 */
		public function setType(string $type) {
			$this->type = strtolower($type);

			if (!ElementType::isValid($this->type))
				throw new \InvalidArgumentException($type . ' is not a recognized element');

			return $this;
		}

		/**
		 * @return int
		 */
		public function getDamage(): int {
			return $this->damage;
		}

		/**
		 * @param int $damage
		 *
		 * @return $this
		 */
		public function setDamage(int $damage) {
			$this->damage = $damage;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function isHidden(): bool {
			return $this->hidden;
		}

		/**
		 * @param bool $hidden
		 *
		 * @return $this
		 */
		public function setHidden(bool $hidden) {
			$this->hidden = $hidden;

			return $this;
		}
	}