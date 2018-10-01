<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="items")
	 *
	 * Class Item
	 *
	 * @package App\Entity
	 */
	class Item implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=64, unique=true)
		 *
		 * @var string
		 */
		private $name;

		/**
		 * @ORM\Column(type="text")
		 *
		 * @var string
		 */
		private $description;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $rarity;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true}, name="_value")
		 *
		 * @var int
		 */
		private $value = 0;

		/**
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $carryLimit = 0;

		/**
		 * Item constructor.
		 *
		 * @param string $name
		 * @param string $description
		 * @param int    $rarity
		 */
		public function __construct(string $name, string $description, int $rarity) {
			$this->name = $name;
			$this->description = $description;
			$this->rarity = $rarity;
		}

		/**
		 * @return string
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
		 * @return string
		 */
		public function getDescription(): string {
			return $this->description;
		}

		/**
		 * @param string $description
		 *
		 * @return $this
		 */
		public function setDescription(string $description) {
			$this->description = $description;

			return $this;
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
		public function setRarity(int $rarity) {
			$this->rarity = $rarity;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getValue(): int {
			return $this->value;
		}

		/**
		 * @param int $value
		 *
		 * @return $this
		 */
		public function setValue(int $value) {
			$this->value = $value;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getCarryLimit(): int {
			return $this->carryLimit;
		}

		/**
		 * @param int $carryLimit
		 *
		 * @return $this
		 */
		public function setCarryLimit(int $carryLimit) {
			$this->carryLimit = $carryLimit;

			return $this;
		}
	}