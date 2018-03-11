<?php
	namespace App\Entity;

	use App\Game\Attribute;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityTrait;

	class Weapon implements EntityInterface, SluggableInterface {
		use EntityTrait;
		use SluggableTrait;
		use AttributableTrait;

		/**
		 * @var string
		 */
		private $name;

		/**
		 * @var string
		 */
		private $type;

		/**
		 * @var int
		 */
		private $rarity;

		/**
		 * @var WeaponUpgradeNode|null
		 */
		private $upgradeNode;

		/**
		 * Weapon constructor.
		 *
		 * @param string $name
		 * @param string $type
		 * @param int    $rarity
		 */
		public function __construct(string $name, string $type, int $rarity) {
			$this->name = $name;
			$this->type = $type;
			$this->rarity = $rarity;

			$this->setSlug($name);
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
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @return int
		 */
		public function getRarity(): int {
			return $this->rarity;
		}

		/**
		 * @param int $rarity
		 *
		 * @return $this
		 */
		public function setRarity(int $rarity): Weapon {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return WeaponUpgradeNode|null
		 */
		public function getUpgradeNode(): ?WeaponUpgradeNode {
			return $this->upgradeNode;
		}
	}