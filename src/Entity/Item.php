<?php
	namespace App\Entity;

	use App\Entity\Strings\ItemStrings;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="items")
	 *
	 * Class Item
	 *
	 * @package App\Entity
	 */
	class Item implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $rarity;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\ItemStrings",
		 *     mappedBy="item",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|ItemStrings[]
		 */
		private $strings;

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
		 * @param int $rarity
		 */
		public function __construct(int $rarity) {
			$this->rarity = $rarity;

			$this->strings = new ArrayCollection();
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

		/**
		 * @return ItemStrings[]|Collection|Selectable
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return ItemStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new ItemStrings($this, $language));

			return $strings;
		}
	}