<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class WeaponElement implements EntityInterface, \JsonSerializable {
		use EntityTrait;

		/**
		 * @var Weapon
		 */
		private $weapon;

		/**
		 * @var string
		 */
		private $type;

		/**
		 * @var int
		 */
		private $damage;

		/**
		 * @var bool
		 */
		private $hidden;

		/**
		 * WeaponElement constructor.
		 *
		 * @param Weapon $weapon
		 * @param string $type
		 * @param int    $damage
		 * @param bool   $hidden
		 */
		public function __construct(Weapon $weapon, string $type, int $damage, bool $hidden = false) {
			$this->weapon = $weapon;
			$this->type = strtolower($type);
			$this->damage = $damage;
			$this->hidden = $hidden;
		}

		/**
		 * @return Weapon
		 */
		public function getWeapon(): Weapon {
			return $this->weapon;
		}

		/**
		 * @return string
		 */
		public function getType(): string {
			return $this->type;
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

		/**
		 * @return array
		 */
		public function jsonSerialize(): array {
			return [
				'type' => $this->getType(),
				'damage' => $this->getDamage(),
				'hidden' => $this->isHidden(),
			];
		}
	}