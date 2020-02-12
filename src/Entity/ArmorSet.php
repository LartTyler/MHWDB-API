<?php
	namespace App\Entity;

	use App\Entity\Strings\ArmorSetStrings;
	use App\Game\Rank;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="armor_sets")
	 *
	 * Class ArmorSet
	 *
	 * @package App\Entity
	 */
	class ArmorSet implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\Choice(callback={"App\Game\Rank", "values"})
		 *
		 * @ORM\Column(type="string", length=16)
		 *
		 * @var string
		 * @see Rank
		 */
		private $rank;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\Armor", mappedBy="armorSet")
		 *
		 * @var Collection|Selectable|Armor[]
		 */
		private $pieces;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\ArmorSetStrings",
		 *     mappedBy="armorSet",
		 *     orphanRemoval=true,
		 *     cascade={"all"},
		 *     fetch="EAGER"
		 * )
		 *
		 * @var Collection|Selectable|ArmorSetStrings[]
		 */
		private $strings;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\ArmorSetBonus")
		 *
		 * @var ArmorSetBonus
		 */
		private $bonus = null;

		/**
		 * ArmorSet constructor.
		 *
		 * @param string $rank
		 *
		 * @see Rank
		 */
		public function __construct(string $rank) {
			$this->rank = $rank;

			$this->pieces = new ArrayCollection();
			$this->strings = new ArrayCollection();
		}

		/**
		 * @return string
		 */
		public function getRank(): string {
			return $this->rank;
		}

		/**
		 * @param string $rank
		 *
		 * @return $this
		 */
		public function setRank(string $rank) {
			$this->rank = $rank;

			return $this;
		}

		/**
		 * @return Armor[]|Collection|Selectable
		 */
		public function getPieces() {
			return $this->pieces;
		}

		/**
		 * @return ArmorSetBonus|null
		 */
		public function getBonus(): ?ArmorSetBonus {
			return $this->bonus;
		}

		/**
		 * @param ArmorSetBonus|null $bonus
		 *
		 * @return $this
		 */
		public function setBonus(?ArmorSetBonus $bonus) {
			$this->bonus = $bonus;

			return $this;
		}

		/**
		 * @return ArmorSetStrings[]|Collection|Selectable
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return ArmorSetStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new ArmorSetStrings($this, $language));

			return $strings;
		}
	}