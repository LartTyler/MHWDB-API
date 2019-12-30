<?php
	namespace App\Entity;

	use App\Entity\Strings\MonsterWeaknessStrings;
	use App\Game\Element;
	use App\Localization\TranslatableEntityInterface;
	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(readOnly=true)
	 * @ORM\Table(name="monster_weaknesses")
	 *
	 * Class MonsterWeakness
	 *
	 * @package App\Entity
	 */
	class MonsterWeakness implements EntityInterface, TranslatableEntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Monster", inversedBy="weaknesses")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Monster
		 */
		private $monster;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Choice(callback={"App\Game\Element", "all"})
		 *
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 * @see Element
		 */
		private $element;

		/**
		 * @Assert\NotBlank()
		 * @Assert\Range(min=1)
		 *
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $stars;

		/**
		 * @Assert\Valid()
		 *
		 * @ORM\OneToMany(
		 *     targetEntity="App\Entity\Strings\MonsterWeaknessStrings",
		 *     mappedBy="monsterWeakness",
		 *     orphanRemoval=true,
		 *     cascade={"all"}
		 * )
		 *
		 * @var Collection|Selectable|MonsterWeaknessStrings[]
		 */
		private $strings;

		/**
		 * MonsterWeakness constructor.
		 *
		 * @param Monster $monster
		 * @param string  $element
		 * @param int     $stars
		 */
		public function __construct(Monster $monster, string $element, int $stars) {
			$this->monster = $monster;
			$this->element = $element;
			$this->stars = $stars;

			$this->strings = new ArrayCollection();
		}

		/**
		 * @return Monster
		 */
		public function getMonster(): Monster {
			return $this->monster;
		}

		/**
		 * @return string
		 */
		public function getElement(): string {
			return $this->element;
		}

		/**
		 * @param string $element
		 *
		 * @return $this
		 */
		public function setElement(string $element) {
			$this->element = $element;

			return $this;
		}

		/**
		 * @return int
		 */
		public function getStars(): int {
			return $this->stars;
		}

		/**
		 * @param int $stars
		 *
		 * @return $this
		 */
		public function setStars(int $stars) {
			$this->stars = $stars;

			return $this;
		}

		/**
		 * @return Collection|Selectable|MonsterWeaknessStrings[]
		 */
		public function getStrings(): Collection {
			return $this->strings;
		}

		/**
		 * @param string $language
		 *
		 * @return MonsterWeaknessStrings
		 */
		public function addStrings(string $language): EntityInterface {
			$this->getStrings()->add($strings = new MonsterWeaknessStrings($this, $language));

			return $strings;
		}
	}