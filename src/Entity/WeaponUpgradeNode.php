<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;

	class WeaponUpgradeNode implements EntityInterface {
		use EntityTrait;

		/**
		 * @var Weapon
		 */
		private $weapon;

		/**
		 * @var bool
		 */
		private $craftable;

		/**
		 * @var WeaponUpgradeNode|null
		 */
		private $previous;

		/**
		 * @var ArrayCollection
		 */
		private $branches;

		/**
		 * WeaponUpgradeNode constructor.
		 *
		 * @param Weapon                 $weapon
		 * @param bool                   $craftable
		 * @param WeaponUpgradeNode|null $previous
		 */
		public function __construct(Weapon $weapon, bool $craftable, ?WeaponUpgradeNode $previous = null) {
			$this->weapon = $weapon;
			$this->craftable = $craftable;
			$this->previous = $previous;

			$this->branches = new ArrayCollection();
		}

		/**
		 * @return Weapon
		 */
		public function getWeapon(): Weapon {
			return $this->weapon;
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
		 * @return WeaponUpgradeNode|null
		 */
		public function getPrevious(): ?WeaponUpgradeNode {
			return $this->previous;
		}

		/**
		 * @param WeaponUpgradeNode $previous
		 *
		 * @return $this
		 */
		public function setPrevious(WeaponUpgradeNode $previous) {
			$this->previous = $previous;

			return $this;
		}

		/**
		 * @return Collection|Selectable|WeaponUpgradeNode[]
		 */
		public function getBranches(): Collection {
			return $this->branches;
		}
	}