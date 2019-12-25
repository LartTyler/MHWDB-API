<?php
	namespace App\Entity;

	use App\Entity\Strings\ArmorSetStrings;
	use App\Game\Rank;
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
	class ArmorSet implements EntityInterface, LengthCachingEntityInterface {
		use EntityTrait;

		/**
		 * @Assert\Choice(callback={"App\Game\Rank", "all"})
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
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\ArmorSetStrings",
		 *     mappedBy="armorSet",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
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
		 * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
		 *
		 * @var int
		 * @internal Used to allow API queries against "pieces.length"
		 */
		private $piecesLength = 0;

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
		 * {@inheritdoc}
		 */
		public function syncLengthFields(): void {
			$this->piecesLength = $this->pieces->count();
		}

		/**
		 * @return ArmorSetStrings[]|Collection|Selectable
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}
	}