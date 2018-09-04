<?php
	namespace App\Entity;

	use DaybreakStudios\Utility\DoctrineEntities\EntityInterface;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(
	 *     name="monster_weaknesses",
	 *     uniqueConstraints={
	 *         @ORM\UniqueConstraint(columns={"monster_id", "element"})
	 *     }
	 * )
	 *
	 * Class MonsterWeakness
	 *
	 * @package App\Entity
	 */
	class MonsterWeakness implements EntityInterface {
		use EntityTrait;

		/**
		 * @ORM\ManyToOne(targetEntity="App\Entity\Monster", inversedBy="weaknesses")
		 * @ORM\JoinColumn(nullable=false)
		 *
		 * @var Monster
		 */
		private $monster;

		/**
		 * @ORM\Column(type="string", length=32)
		 *
		 * @var string
		 */
		private $element;

		/**
		 * @ORM\Column(type="smallint", options={"unsigned": true})
		 *
		 * @var int
		 */
		private $stars;

		/**
		 * @ORM\Column(type="text", nullable=true)
		 *
		 * @var null|string
		 */
		private $condition = null;

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
		 * @return null|string
		 */
		public function getCondition(): ?string {
			return $this->condition;
		}

		/**
		 * @param null|string $condition
		 *
		 * @return $this
		 */
		public function setCondition(?string $condition) {
			$this->condition = $condition;

			return $this;
		}
	}